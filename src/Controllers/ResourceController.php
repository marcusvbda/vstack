<?php

namespace marcusvbda\vstack\Controllers;

use App\Http\Controllers\Controller;
use ResourcesHelpers;
use Illuminate\Http\Request;
use Storage;
use marcusvbda\vstack\Services\Messages;
use marcusvbda\vstack\Models\CustomResourceCard;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use marcusvbda\vstack\Exports\{DefaultGlobalExporter, GlobalExporter};
use marcusvbda\vstack\Imports\GlobalImporter;
use Maatwebsite\Excel\HeadingRowImport;
use Excel;
use marcusvbda\vstack\Services\SendMail;

class ResourceController extends Controller
{
    public function index($resource, Request $request)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!$resource->canViewList()) abort(403);
        $data = $this->getData($resource, $request);
        $data = $data->paginate($resource->resultsPerPage());
        $data->map(function ($query) {
            $query->setAppends([]);
        });
        if (@$request["list_type"]) session([$resource->id . "_list_type" => $request["list_type"]]);
        return view("vStack::resources.index", compact("resource", "data"));
    }

    public function getData($resource, Request $request, $query = null)
    {
        $table = $resource->model->getTable() . ".";
        $data      = $request->all();

        $orderBy   = $table . Arr::get($data, 'order_by', "id");
        $orderType = Arr::get($data, 'order_type', "desc");

        $query     = $query ? $query : $resource->model->select($table . "id")->where($table . "id", ">", 0);
        $query->orderBy($orderBy, $orderType);

        foreach ($resource->filters() as $filter) $query = $filter->applyFilter($query, $data);
        $search = $resource->search();

        if (@$data["_"]) {
            $query = $query->where(function ($q) use ($search, $data, $table) {
                foreach ($search as $s) {
                    if (is_callable($s)) $q = $s($q, @$data["_"]);
                    else  $q = $q->OrWhere($table . $s, "like", "%" . (@$data["_"] ? $data["_"] : "") . "%");
                }
                return $q;
            });
        }

        foreach ($resource->lenses() as $len) {
            $field = $len["field"];
            if (isset($data[$field])) {
                $value = $data[$field];
                $query = $query->where($field, $value);
            }
        }
        return $query->orderBy($orderBy, $orderType);
    }

    public function create($resource, Request $request)
    {
        $params = @$request["params"] ? $request["params"] : [];
        $resource = ResourcesHelpers::find($resource);
        if (!$resource->canCreate()) abort(403);
        $data = $this->makeCrudData($resource);
        $data["page_type"] = "Cadastro";
        return view("vStack::resources.crud", compact("resource", "data", "params", "content"));
    }

    public function import($resource)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!($resource->canImport() && $resource->canCreate())) abort(403);
        $data = $this->makeImportData($resource);
        return view("vStack::resources.import", compact('data'));
    }

    public function importSheetTemplate($resource)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!($resource->canImport() && $resource->canCreate())) abort(403);
        $filename = $resource->id . "_" . Carbon::now()->format('Y_m_d_H_i_s') . '_' . Auth::user()->tenant->name . ".xls";
        $exporter = new DefaultGlobalExporter($this->getImporterCollumns($resource));
        Excel::store($exporter, $filename, "local");
        $full_path = storage_path("app/$filename");
        return response()->download($full_path)->deleteFileAfterSend(true);
    }

    protected function getImporterCollumns($resource)
    {
        $columns = [];
        foreach ($resource->getTableColumns() as $col) {
            if (!in_array($col, ["created_at", "deleted_at", "updated_at", "email_verified_at", "confirmation_token", "recovery_token", "password", "tenant_id"])) $columns[] = $col;
        }
        return $columns;
    }

    protected function makeImportData($resource)
    {
        return [
            "resource" => [
                "resource_id"    => $resource->id,
                "label"          => $resource->label(),
                "singular_label" => $resource->singularLabel(),
                "route"          => $resource->route(),
                "columns"        => $this->getImporterCollumns($resource)
            ]
        ];
    }

    public function checkFileImport($resource, Request $request)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!($resource->canImport() && $resource->canCreate())) abort(403);
        $file = $request->file("file");
        if (!$file) return ["success" => false, "message" => ["type" => "error", "text" => "Arquivo inválido..."]];
        if ($file->getSize() > 137072) return ["success" => false, "message" => ["type" => "error", "text" => "Arquivo maior do que o permitido..."]];
        $data = Excel::toArray(new HeadingRowImport, $file);
        $header = @$data[0][0];
        if (!@$data[0][0])
            return ["success" => false, "message" => ["type" => "error", "text" => "Cabeçalho da planilha nao encontrado"]];
        return ["success" => true, "data" => $header];
    }

    public function importSubmit($resource, Request $request)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!($resource->canImport() && $resource->canCreate())) abort(403);
        $data = $request->all();
        $file = $data["file"];
        if (!$file) return ["success" => false, "message" => ["type" => "error", "text" => "Arquivo inválido..."]];
        if ($file->getSize() > 137072) return ["success" => false, "message" => ["type" => "error", "text" => "Arquivo maior do que o permitido..."]];

        $config = json_decode($data["config"]);
        $fieldlist = $config->fieldlist;
        $filename = Auth::user()->tenant_id . "_" . uniqid() . ".xls";
        $filepath = $file->storeAs('local', $filename);
        $user = Auth::user();
        $tenant_id = array_search("tenant_id", $resource->getTableColumns()) === false ? null : $user->tenant_id;

        dispatch(function () use ($resource, $fieldlist, $filepath, $tenant_id, $user) {
            $importer = new GlobalImporter($filepath, ResourceController::class, 'sheetImportRow', compact('resource', 'fieldlist', 'filepath', 'tenant_id'));
            Excel::import($importer, $importer->getFile());
            $result = $importer->getResult();
            if (@$result["success"]) {
                $message = "Foi importado com sucesso sua planilha de " . $resource->label() . ". (" . $result['qty'] . " Registro" . ($result['qty'] > 1 ? 's' : '') . ")";
            } else {
                $message = "Erro na importação de planilha de " . $resource->label() . " ( " . $result["error"]['message'] . " )";
            }
            DB::table("notifications")->insert([
                "type" => 'App\Notifications\CustomerNotification',
                "notifiable_type" => 'App\User',
                "notifiable_id" => $user->id,
                "alert_type" => 'vstack_alert',
                "tenant_id" => $user->tenant_id,
                "created_at" => carbon::now(),
                "data" => json_encode([
                    "message" => $message,
                    "type" => @$result["success"] ? 'success' : 'error'
                ]),
            ]);
        })->onQueue("resource-import");


        return ["success" => true];
    }

    public function export($resource, Request $request)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!$resource->canExport()) abort(403);
        $user = Auth::user();
        $data = $request->all();
        $_request = new Request();
        $_request->setMethod('POST');
        $params = [];
        foreach ($data["get_params"] as $key => $value) $params[$key] = $value;
        $_request->request->add($params);
        $result = $this->getData($resource, $_request);
        $filename =    'Relatório de ' . $resource->id . '_' . Carbon::now()->format('Y_m_d_H_i_s') . '_' . $user->tenant->name . '.xls';
        $ids =  $result->pluck("id")->all();
        return $this->exportSheetOrDispatch($user, count($ids), $ids, $resource, $data['columns'], $filename);
    }

    public function exportSheetOrDispatch($user, $count, $ids, $resource, $columns, $filename)
    {
        if ($count <= $resource->maxRowsExportSync()) {
            try {
                $exporter = new GlobalExporter($resource, $columns, $resource->model->whereIn("id", $ids)->get());
                Excel::store($exporter, $filename, "local");
                $message = "Planilha de " . $resource->label() . " exportada com sucesso";
                return ['success' => true, 'message_type' => 'success', 'message' => $message, 'url' => route('resource.export_download', ['resource' => $resource->id, 'file' => $filename])];
            } catch (\Exception $e) {
                $message = "Erro na exportação de planilha de " . $resource->label() . " ( " . $e->getMessage() . " )";
                return ['success' => false, 'message_type' => 'error', 'message' => $message];
            }
        }
        dispatch(function () use ($user, $resource, $columns, $ids, $filename) {
            try {
                $exporter = new GlobalExporter($resource, $columns, $resource->model->whereIn("id", $ids)->get());
                Excel::store($exporter, $filename, "local");
                $url = route('resource.export_download', ['resource' => $resource->id, 'file' => $filename]);
                DB::table("notifications")->insert([
                    "type" => 'App\Notifications\CustomerNotification',
                    "notifiable_type" => 'App\User',
                    "notifiable_id" => $user->id,
                    "alert_type" => 'vstack_alert',
                    "tenant_id" => $user->tenant_id,
                    "created_at" => carbon::now(),
                    "data" => json_encode([
                        "message" => "Sua planilha de " . $resource->label() . " foi exportada com sucesso e o arquivo foi enviado para seu email, " . $user->email,
                        "type" => 'success'
                    ]),
                ]);
                $appName = config("app.name");
                $user->save();
                $user->refresh();
                $html = "
                <p>Olá {$user->name},</p>
                <p>Aqui está sua planilha de " . $resource->label() . "</p>
                <p>Clique <a href='{$url}' target='_BLANK'>aqui</a> abaixo para efetuar o download</p>
                <p style='margin-top:30px'>Obrigado, {$appName}";
                SendMail::to($user->email, "Planilha de " . $resource->label(), $html);
            } catch (\Exception $e) {
                $message = "Erro na exportação de planilha de " . $resource->label() . " ( " . $e->getMessage() . " )";
                DB::table("notifications")->insert([
                    "type" => 'App\Notifications\CustomerNotification',
                    "notifiable_type" => 'App\User',
                    "notifiable_id" => $user->id,
                    "alert_type" => 'vstack_alert',
                    "tenant_id" => $user->tenant_id,
                    "created_at" => carbon::now(),
                    "data" => json_encode([
                        "message" => $message,
                        "type" => 'error'
                    ]),
                ]);
                return ['success' => false, 'message' => $message];
            }
        })->onQueue("resource-import");
        $message = "Sua Planinha de " . $resource->label() . " está sendo exportada, e assim que o processo for concluido você será notificado e o arquivo será enviado em seu email, isso pode levar alguns minutos.";
        return ['success' => true, 'message_type' => 'info', 'message' => $message];
    }

    public function exportDownload($resource, $file)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!$resource->canExport()) abort(403);
        $full_path = storage_path("app/$file");
        return response()->download($full_path)->deleteFileAfterSend(true);
    }

    public function sheetImportRow($rows, $params, $importer)
    {
        extract($params);
        $qty = 0;
        try {
            DB::beginTransaction();
            foreach ($rows as $key => $row_values) {
                if ($key == 0) continue;
                $row_values = $row_values->toArray();
                $new = [];
                foreach ($fieldlist as $field => $row_key) {
                    if ($row_key == "_IGNORE_") continue;
                    $value = @$row_values[array_search($row_key, $importer->headers)];
                    if (!$value) continue;
                    $new[$field] = $value;
                }
                $new_model = @$new["id"] ? $resource->model->findOrFail($new["id"]) : new $resource->model;
                $new["tenant_id"] = $tenant_id;
                $new_model->fill($new);
                $new_model->save();
                unset($new_model, $row_values, $new);
                $qty++;
            }
            DB::commit();
            $importer->setResult([
                'success' => true,
                'qty' => $qty
            ]);
        } catch (\Exception $e) {
            $importer->setResult([
                'success' => false,
                'error' => [
                    "message" => $e->getMessage(),
                    "line" => $key
                ]
            ]);
            DB::rollback();
        }
    }

    public function edit($resource, $code, Request $request)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!$resource->canUpdate()) abort(403);
        $content = $resource->model->findOrFail($code);
        $data = $this->makeCrudData($resource, $content);
        $data["page_type"] = "Edição";
        $params = @$request["params"] ? $request["params"] : [];
        return view("vStack::resources.crud", compact("resource", "data", "params", "content"));
    }

    public function destroy($resource, $code)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!$resource->canDelete()) abort(403);
        $content = $resource->model->findOrFail($code);
        if ($content->delete()) {
            Messages::send("success", "Registro excluido com sucesso !!");
            return ["success" => true, "route" => $resource->route()];
        }
        Messages::send("error", " Erro ao excluir com " . $resource->singularLabel() . " !!");
        return ["success" => false,  "route" => $resource->route()];
    }

    public function destroyField($resource, $code)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!$resource->canDelete()) abort(403);
        $content = $resource->model->findOrFail($code);
        if ($content->delete()) return ["success" => true];
        return ["success" => false];
    }

    public function view(Request $request, $resource, $code)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!$resource->canView()) abort(403);
        $content = $resource->model->findOrFail($code);
        $data = $this->makeViewData($content->code, $resource, $content);
        $data["page_type"] = "Visualização";
        $params = @$request["params"] ? $request["params"] : [];
        return view("vStack::resources.view", compact("resource", "data", "params", "content"));
    }

    public function makeViewData($code, $resource, $content = null)
    {
        $route = $resource->route();
        return [
            "fields"        => $this->makeViewDataFields($content, $resource->fields()),
            "can_update"    => $resource->canUpdate(),
            "can_delete"    => $resource->canDelete(),
            "route"         => $route . "/" . $code,
            "update_route"  => $route . "/" . $code . "/edit",
            "route_destroy" => $route . "/" . $code . "/destroy",
        ];
    }

    protected function makeViewDataFields($content, $fields)
    {
        $data = [];
        if (!$content) return $fields;
        foreach ($fields  as $card) {
            $_card = [
                "label"  => $card->label,
                "inputs" => []
            ];
            foreach ($card->inputs  as $field) {
                if (!in_array($field->options["field"], ["password", "password_confirmation"])) {
                    switch ($field->options["type"]) {
                        case "text":
                            $_card["inputs"][$field->options["label"]] = @$content->{$field->options["field"]};
                            break;
                        case "check":
                            $_card["inputs"][$field->options["label"]] = @$content->{$field->options["field"]} ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-danger">Não</span>';
                            break;
                        case "belongsTo":
                            if (@$field->options["model"]) {
                                $model = $field->options["model"];
                                $value = @app()->make($model)->find($content->{$field->options["field"]})->name;
                                $_card["inputs"][$field->options["label"]] = $value;
                            } else $_card["inputs"][$field->options["label"]] = $content->{$field->options["field"]};
                            break;
                        case "belongsToMany":
                            $value = implode(",", $content->{$field->options["field"]}->pluck(@$field->options["pluck_value"] ? $field->options["pluck_value"] : "value")->toArray());
                            $_card["inputs"][$field->options["label"]] = $value;
                            break;
                        case "morphsMany":
                            $value = implode(",", $content->{$field->options["field"]}->pluck("value")->toArray());
                            $_card["inputs"][$field->options["label"]] = $value;
                            break;
                        case "upload":
                            if (!@$content->casts[$field->options["field"]])
                                $array = $content ? @$content->{$field->options["field"]}->pluck("value")->toArray() : [];
                            else
                                $array = @$content->{$field->options["field"]} ? @$content->{$field->options["field"]} : null;

                            $array = $array ? $array : [];
                            if (!is_array($array)) $array = [$array];
                            foreach ($array as $row) {
                                @$_card["inputs"][$field->options["label"]] .= "<p class='my-0'><a class='link preview' target='_BLANK' href='" . $row . "'>" . $row . "</a></p>";
                            }
                            break;
                        case "url":
                            $_card["inputs"][$field->options["label"]] = "<a class='link preview' target='_BLANK' href='" . @$content->{$field->options["field"]} . "'>" . @$content->{$field->options["field"]} . "</a>";
                            break;
                        case "resource-field":
                            $_resource = ResourcesHelpers::find(strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $field->options["resource"])));
                            foreach ($field->options["params"] as $key => $value) $params[$key] = @$content->{$value} ? $content->{$value} : $value;
                            $view = $field->getView();
                            $target = substr($view, strpos($view, ":params='"), strpos($view, "' end_params"));
                            $view = str_replace($target, ":params='" . json_encode($params) . "' />", $view);
                            $_card["inputs"]["IGNORE__" . $_resource->label()] = $view;
                            break;
                        case "custom":
                            $custom_params = "";
                            foreach ($field->options['params'] as $custom_key => $custom_value) {
                                eval("\$custom_value = \"$custom_value\";");
                                $custom_params .= ":$custom_key='$custom_value' ";
                            }
                            $custom_oldView = $field->view;
                            $custom_view = str_replace(" />", " $custom_params  />", $custom_oldView);
                            $_card["inputs"]["IGNORE__" . uniqid()] = $custom_view;
                            break;
                        default:
                            $_card["inputs"][$field->options["label"]] = @$content->{$field->options["field"]};
                            break;
                    }
                }
            }
            $data[] = $_card;
        }
        return $data;
    }

    protected function makeCrudData($resource, $content = null)
    {
        return [
            "id"          => @$content->id,
            "fields"      => $this->makeCrudDataFields($content, $resource->fields()),
            "store_route" => route('resource.store', ["resource" => $resource->id]),
            "list_route"  => route('resource.index', ["resource" => $resource->id]),
            "resource_id" => $resource->id
        ];
    }

    protected function makeCrudDataFields($content, $cards)
    {
        foreach ($cards  as $card) {
            foreach ($card->inputs  as $input) {
                switch ($input->options["type"]) {
                    case "belongsToMany":
                        $input->options["value"] = $content ? @$content->{$input->options["field"]}->pluck("id")->toArray() : null;
                        break;
                    case "morphsMany":
                        $input->options["value"] = $content ? @$content->{$input->options["field"]}->pluck("value")->toArray() : null;
                        break;
                    case "upload":
                        if (!@$content->casts[$input->options["field"]])
                            $input->options["value"] = $content ? @$content->{$input->options["field"]}->pluck("value")->toArray() : null;
                        else {
                            $view = $input->getView();
                            $oldView = $view;
                            $value = @$content->{$input->options["field"]};
                            if (!is_array($value)) $value = [$value];
                            $input->options["value"] = $value ? $value : null;
                        }
                        break;
                    case "resource-field":
                        $params = [];
                        foreach ($input->options["params"] as $key => $value) $params[$key] = @$content->{$value} ? $content->{$value} : $value;
                        $view = $input->getView();
                        $oldView = $view;
                        $target = substr($view, strpos($view, ":params='"), strpos($view, "' end_params"));
                        $view = str_replace($target, ":params='" . json_encode($params) . "' />", $view);
                        $input->view = $view;
                        $card->view = str_replace($oldView, $view, $card->view);
                        break;
                    case "custom":
                        if (@$content) {
                            $params = "";
                            foreach ($input->options['params'] as $key => $value) {
                                eval("\$value = \"$value\";");
                                $params .= ":$key='$value' ";
                            }
                            $oldView = $input->view;
                            $view = str_replace(" />", " $params  />", $oldView);
                            $card->view = str_replace($oldView, $view, $card->view);
                        } else  $card->view = "";
                        break;
                    default:
                        $input->options["value"] = ($input->options["field"] == "password") ? null : @$content->{$input->options["field"]};
                        break;
                }
            }
        }
        return $cards;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if (!@$data["resource_id"]) abort(404);
        $resource = ResourcesHelpers::find($data["resource_id"]);
        if (@$data["id"]) if (!$resource->canUpdate()) abort(403);
        if (!@$data["id"]) if (!$resource->canCreate()) abort(403);
        $validation_custom_message =  $resource->getValidationRuleMessage();
        $this->validate($request, $resource->getValidationRule(), @$validation_custom_message ? $validation_custom_message : []);
        $target = @$data["id"] ? $resource->model->findOrFail($data["id"]) : new $resource->model();
        $data = $request->except(["resource_id", "id", "redirect_back"]);
        $data = $this->processStoreData($resource, $data);
        $target->fill($data["data"]);
        $target->save();
        $this->storeBelongsToMany($target, $data["belongsToMany"]);
        $this->storeMorphsMany($target, $data["morphsMany"]);
        $this->storeUploads($target, $data["upload"]);
        Messages::send("success", "Registro salvo com sucesso !!");
        return ["success" => true, "route" => route('resource.index', ["resource" => $resource->id])];
    }

    public function storeField(Request $request)
    {
        $data = $request->all();
        if (!@$data["resource_id"]) abort(404);
        $resource = ResourcesHelpers::find($data["resource_id"]);
        if (@$data["id"]) if (!$resource->canUpdate()) abort(403);
        if (!@$data["id"]) if (!$resource->canCreate()) abort(403);
        $this->validate($request, $resource->getValidationRule());
        $target = @$data["id"] ? $resource->model->findOrFail($data["id"]) : new $resource->model();
        $data = $request->except(["resource_id", "id", "redirect_back"]);
        $data = $this->processStoreData($resource, $data);
        $target->fill($data["data"]);
        $target->save();
        $this->storeBelongsToMany($target, $data["belongsToMany"]);
        $this->storeMorphsMany($target, $data["morphsMany"]);
        $this->storeUploads($target, $data["upload"]);
        return ["success" => true, "route" => route('resource.index', ["resource" => $resource->id])];
    }

    protected function storeUploads($target, $relations)
    {
        $target->refresh();
        foreach ($relations as $key => $values) {
            if (is_callable($target->{$key})) {
                $target->{$key}()->delete();
                if ($values) {
                    foreach ($values as $value) {
                        $target->{$key}()->create(["value" => $value]);
                    }
                }
            } else {
                $target->{$key} = $values;
                $target->save();
            }
        }
    }

    protected function storeMorphsMany($target, $relations)
    {
        $target->refresh();
        foreach ($relations as $key => $values) {
            $target->{$key}()->delete();
            if ($values) {
                foreach ($values as $value) {
                    $target->{$key}()->create(["value" => $value]);
                }
            }
        }
    }

    protected function storeBelongsToMany($target, $relations)
    {
        $target->refresh();
        foreach ($relations as $key => $value) {
            $target->{$key}()->sync($value);
        }
    }

    protected function processStoreData($resource, $data)
    {
        $result = $this->getBelongsToManyFields($resource, $data);
        $result = $this->getMorphsManyFields($resource, $result);
        $result = $this->getUploadsFields($resource, $result);
        unset($result["data"][""]);
        return $result;
    }

    protected function getUploadsFields($resource, $result)
    {
        $fields = [];
        foreach ($resource->fields() as $cards) {
            foreach ($cards->inputs as $field) {
                if ($field->options["type"] == "upload") {
                    @$fields[$field->options["field"]] = $result["data"][$field->options["field"]];
                    unset($result["data"][$field->options["field"]]);
                }
            }
        }
        $result["upload"] = $fields;
        return $result;
    }

    protected function getMorphsManyFields($resource, $result)
    {
        $fields = [];
        foreach ($resource->fields() as $cards) {
            foreach ($cards->inputs as $field) {
                if ($field->options["type"] == "morphsMany") {
                    @$fields[$field->options["field"]] = $result["data"][$field->options["field"]];
                    unset($result["data"][$field->options["field"]]);
                }
            }
        }
        $result["morphsMany"] = $fields;
        return $result;
    }

    protected function getBelongsToManyFields($resource, $data)
    {
        $fields = [];
        foreach ($resource->fields() as $cards) {
            foreach ($cards->inputs as $field) {
                if ($field->options["type"] == "belongsToMany") {
                    $fields[$field->options["field"]] = $data[$field->options["field"]];
                    unset($data[$field->options["field"]]);
                }
            }
        }
        return ["belongsToMany" => $fields, "data" => $data];
    }

    public function customCard($resource)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!$resource->canCustomizeMetrics()) abort(403);
        $query = CustomResourceCard::where("resource_id", $resource->id);
        $cards = $query->paginate($resource->resultsPerPage());
        return view("vStack::resources.custom_cards_index", compact("resource", "cards"));
    }

    public function customCardDestroy($resource, $code)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!$resource->canCustomizeMetrics()) abort(403);
        $content = CustomResourceCard::where("resource_id", $resource->id)->where("id", $code)->firstOrFail();
        if ($content->delete()) {
            Messages::send("success", "Registro excluido com sucesso !!");
            return ["success" => true, "route" => $resource->route() . "/custom-cards"];
        }
        Messages::send("error", " Erro ao excluir com " . $resource->singularLabel() . " !!");
        return ["success" => false,  "route" => $resource->route()];
    }

    public function customCardEdit($resource, $code)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!$resource->canCustomizeMetrics()) abort(403);
        $card = CustomResourceCard::where("resource_id", $resource->id)->where("id", $code)->firstOrFail();
        $data = ["page_type" => "Edição"];
        return view("vStack::resources.custom_cards_crud", compact("resource", "card", "data"));
    }

    public function customMetricCalculate($resource, $code, Request $request)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!$resource->canCustomizeMetrics()) abort(403);
        $card = CustomResourceCard::where("resource_id", $resource->id)->where("id", $code)->firstOrFail();
        if ($card->type == "trend-counter") return $this->customTrendCounterCalculate($resource, $card, $request["range"]);
        if ($card->type == "group-chart") return $this->customGroupChartCalculate($resource, $card);
        if ($card->type == "trend-chart") return $this->customTrendChartCalculate($resource, $card, $request["range"]);
        if ($card->type == "bar-chart") return $this->customBarChartCalculate($resource, $card, $request["range"]);
    }

    protected function customBarChartCalculate($resource, $card, $range)
    {
        $startDate = Carbon::create($range[0])->format("Y-m-d 00:00:00");
        $endDate   = Carbon::create($range[1])->format("Y-m-d 00:00:00");
        return  $resource->model->whereRaw(DB::raw("DATE(" . $card->group_by . ") >='$startDate'" . " and " . "DATE(" . $card->group_by . ") <='$endDate'"))
            ->select(DB::raw('DATE_FORMAT(' . $card->group_by . ',"%d/%m/%Y") as formated_date, count(*) as qty'))
            ->groupBy("formated_date")
            ->pluck('qty', 'formated_date');
    }

    protected function customTrendChartCalculate($resource, $card, $range)
    {
        $startDate = Carbon::create($range[0])->format("Y-m-d 00:00:00");
        $endDate   = Carbon::create($range[1])->format("Y-m-d 00:00:00");
        return  $resource->model->whereRaw(DB::raw("DATE(" . $card->group_by . ") >='$startDate'" . " and " . "DATE(" . $card->group_by . ") <='$endDate'"))
            ->select(DB::raw('DATE_FORMAT(' . $card->group_by . ',"%d/%m/%Y") as formated_date, count(*) as qty'))
            ->groupBy("formated_date")
            ->pluck('qty', 'formated_date');
    }

    protected function customGroupChartCalculate($resource, $card)
    {
        if (!strpos($card->group_by, "->")) {
            $results = [];
            foreach ($resource->model->groupBy($card->group_by)->select($card->group_by)->get() as $legend)
                $results[(is_bool($legend->{$card->group_by}) ? ($legend->{$card->group_by} ? "Sim  " : "Não") : $legend->{$card->group_by})] =  $resource->model->where($card->group_by, $legend->{$card->group_by})->count();
            return $results;
        } else {
            $indexes = explode("->", $card->group_by);
            $results = [];
            $options = $resource->customMetricOptions();
            $options = $options["group-chart"];
            $options = array_filter($options, function ($x) use ($card) {
                if ($x["id"] == $card->group_by) return $x;
            });
            foreach ($resource->model->get() as $row)
                $results[$row->{$indexes[0]}->{$indexes[1]}] = $resource->model->where($options[0]["key"], $row->{$indexes[0]}->id)->count();
            return $results;
        }
    }

    protected function customTrendCounterCalculate($resource, $card, $range)
    {
        $total = $resource->model->count();
        if ($total <= 0) return ["value" => 0, "average" => 0];
        $startDate = Carbon::create($range[0])->format("Y-m-d 00:00:00");
        $endDate   = Carbon::create($range[1])->format("Y-m-d 00:00:00");
        $results = $resource->model->whereRaw(DB::raw("DATE(created_at) >='$startDate'" . " and " . "DATE(created_at) <='$endDate'"))
            ->select(DB::raw('DATE_FORMAT(created_at,"%d/%m/%Y") as formated_date, count(*) as qty'))
            ->groupBy("formated_date")
            ->pluck('qty', 'formated_date');
        $value = 0;
        if (count($results) > 0) {
            foreach ($results as $result) $value += $result;
            $value = $value / count($results);
        }
        $total_results = $resource->model->select(DB::raw('DATE_FORMAT(created_at,"%d/%m/%Y") as formated_date, count(*) as qty'))
            ->groupBy("formated_date")
            ->pluck('qty', 'formated_date');
        $average = 0;
        if (count($total_results) > 0) {
            foreach ($total_results as $result) $average += $result;
            $average = $average / count($total_results);
        }
        return [
            "value"   => $value,
            "compare" => $average
        ];
    }

    public function customCardCreate($resource)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!$resource->canCustomizeMetrics()) abort(403);
        $data = ["page_type" => "Cadastro"];
        return view("vStack::resources.custom_cards_crud", compact("resource", "data"));
    }

    public function customCardStore($resource, Request $request)
    {
        $resource = ResourcesHelpers::find($resource);
        if (!$resource->canCustomizeMetrics()) abort(403);
        $data = $request->all();
        $card = @$data["id"] ? CustomResourceCard::findOrFail($data["id"]) : new CustomResourceCard();
        $data["resource_id"] = $resource->id;
        if ($data["type"] == "custom-content") {
            unset($data["group_by"]);
            unset($data["update_interval"]);
            $card->fill($data);
        }
        if ($data["type"] == "trend-counter") {
            unset($data["group_by"]);
            unset($data["subtitle"]);
            unset($data["content"]);
            $card->fill($data);
        }
        if (in_array($data["type"], ["group-chart", "trend-chart", "bar-chart"])) {
            if (in_array($data["type"], ["trend-chart", "bar-chart"])) unset($data["subtitle"]);
            unset($data["content"]);
            $card->fill($data);
        }
        Messages::send("success", "Card Customizado Adicionado com Sucesso !!!");
        $card->save();
        return ["success" => true];
    }

    public function option_list(Request $request)
    {
        try {
            $model = app()->make($request["model"]);
            return ["success" => true, "data" => $model->get()];
        } catch (\Exception $e) {
            return ["success" => false, "data" => []];
        }
    }

    public function globalSearch(Request $request)
    {
        $data = [];
        $filter = $request["filter"];
        foreach (ResourcesHelpers::all() as $resource) {
            $keys = array_keys($resource);
            $resource = $resource[$keys[0]];
            if ($resource->globallySearchable() && $resource->canView()) {
                $search_indexes = $resource->search();
                $query = $resource->model->where("id", ">", 0);
                $query = $query->where(function ($q) use ($search_indexes, $filter) {
                    foreach ($search_indexes as $index) $q = $q->OrWhere($index, "like", "%$filter%");
                    return $q;
                });
                $label = $resource->singularLabel();
                foreach ($query->get() as $row) {
                    $data[] = [
                        "resource" => $label,
                        "name"     => $row->name,
                        "link"     => $resource->route() . "/" . $row->code
                    ];
                }
            }
        }
        return ["data" => $data];
    }

    public function metricCalculate($resource, $key, Request $request)
    {
        $resource = ResourcesHelpers::find($resource);
        $metric = null;
        foreach ($resource->metrics() as $m) {
            if ($m->uriKey() == $key) {
                $metric = $m;
                break;
            }
        }
        if (!$metric) abort(404);
        return $metric->calculate($request);
    }

    public function upload(Request $request)
    {
        if (is_string($request['file'])) {
            $url = $request['file'];
            $name = pathinfo($url, PATHINFO_FILENAME) . ".jpg";
            Storage::put(
                "public/$name",
                file_get_contents($url)
            );
            return ["path" => asset("storage/$name")];
        }
        return ["path" => asset(str_replace("public", "storage", $request->file('file')->store('public')))];
    }

    public function fieldData($resource, Request $request)
    {
        $resource = ResourcesHelpers::find($resource);
        $params = $request->except(["redirect_back"]);
        $query = $resource->model->where("id", ">", 0);
        foreach ($params as $key => $value) $query = $query->where($key, $value);
        $data = $this->getData($resource, $request, $query);
        $data = $data->get();
        $params = $request->all();
        $crud_data = $this->fieldDataProcessCrudData($resource, $params);
        $rendered_data = [
            "resource_id" => $resource->id,
            "index_label" => @$resource->indexLabel(),
            "label" => $resource->label(),
            "singular_label" => $resource->singularLabel(),
            "can_create"  => $resource->canCreate(),
            "can_update"  => $resource->canUpdate(),
            "can_delete"  => $resource->canDelete(),
            "model_count" => $resource->model->count(),
            "store_button_label" => $resource->storeButtonLabel(),
            "no_results_found_text" => $resource->noResultsFoundText(),
            "data" => $data,
            "data_count" => $data->count(),
            "icon" => $resource->icon(),
            "nothing_stored_text" => $resource->nothingStoredText(),
            "nothing_stored_subtext" => $resource->nothingStoredSubText(),
            "crud_fields" =>  $crud_data,
            "params" => $params,
            "store_route" => route('resource.field.store', ["resource" => $resource->id]),
            "table" => $resource->table(),
            "destroy_route" => route("resource.field.destroy", ["resource" => $resource->id, "id" => "_replace_area_"])
        ];
        return response()->json($rendered_data);
    }

    protected function fieldDataProcessCrudData($resource, $params)
    {
        $fields = [];
        $crud_data = $this->makeCrudData($resource);
        for ($i = 0; $i < count($crud_data["fields"]); $i++) {
            for ($y = 0; $y < count($crud_data["fields"][$i]->inputs); $y++) {
                $field = $crud_data["fields"][$i]->inputs[$y];
                if (@$params[$crud_data["fields"][$i]->inputs[$y]->options['field']] != null) {
                    $field->options["value"] = $params[$crud_data["fields"][$i]->inputs[$y]->options['field']];
                    $field->options["visible"] = false;
                    $field->options["disabled"] = true;
                }
                $field->getView();
                if ($field->options["type"] == "resource-field") {
                    $field->view  = str_replace("/>", "v-if='form.id' />", $field->view);
                }
                $fields[] = $field;
            }
        }
        return $fields;
    }
}
