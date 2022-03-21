<?php

use marcusvbda\vstack\Controllers\{
	VstackController,
	ResourceController
};

Route::group(['prefix' => "vstack"], function () {
	Route::group(['middleware' => ['web', 'auth']], function () {
		Route::get('grapes-editor', [VstackController::class, 'grapesEditor']);
		Route::post('json-api', [VstackController::class, 'getJson']);
		Route::post('{resource}/get-partial-content', [VstackController::class, 'getPartialContent'])->name("vstack.get_partials_content");
	});
});

Route::group(['prefix' => "admin"], function () {
	Route::group(['middleware' => ['web', 'auth']], function () {
		Route::post('upload', [ResourceController::class, 'upload'])->name("resource.upload");
		Route::get('relatorios/{resource}', [ResourceController::class, 'report'])->name("resource.report");
		Route::post('relatorios/{resource}/create-report-template', [ResourceController::class, 'createReportTemplate'])->name("resource.create.report.template");
		Route::get('{resource}', [ResourceController::class, 'index'])->name("resource.index");
		Route::post('{resource}/field-data', [ResourceController::class, 'fieldData'])->name("resource.fielddata");
		Route::get('{resource}/create', [ResourceController::class, 'create'])->name("resource.create");
		Route::post('{resource}/action/{id}', [ResourceController::class, 'handlerAction'])->name("resource.action.submit");
		Route::post('{resource}/get-resource-crud-content', [ResourceController::class, 'getResourceCrudContent'])->name("resource.dialog_content");
		Route::post('{resource}/store', [ResourceController::class, 'store'])->name("resource.store");
		Route::post('{resource}/store-wizard-step-validation', [ResourceController::class, 'storeWizardStepValidation'])->name("resource.store-wizard-step-validation");
		Route::post('{resource}/field/store', [ResourceController::class, 'storeField'])->name("resource.field.store");
		Route::get('{resource}/import', [ResourceController::class, 'import'])->name("resource.import");
		Route::post('{resource}/export', [ResourceController::class, 'export'])->name("resource.export");
		Route::get('{resource}/import/sheet_template', [ResourceController::class, 'importSheetTemplate'])->name("resource.import.check_file");
		Route::post('{resource}/import/check_file', [ResourceController::class, 'checkFileImport'])->name("resource.import.check_file");
		Route::post('{resource}/import/submit', [ResourceController::class, 'importSubmit'])->name("resource.import.submit");
		Route::get('{resource}/{code}', [ResourceController::class, 'view'])->middleware(['hashids:code'])->name("resource.view");
		Route::post('{resource}/{code}/clone', [ResourceController::class, 'clone'])->middleware(['hashids:code'])->name("resource.clone");
		Route::get('{resource}/tags/options', [ResourceController::class, 'tagOptions'])->name("resource.optionTags");
		Route::get('{resource}/{code}/tags', [ResourceController::class, 'getTags'])->middleware(['hashids:code'])->name("resource.getTags");
		Route::delete('{resource}/{code}/tags/destroy/{id}', [ResourceController::class, 'destroyTag'])->middleware(['hashids:code'])->name("resource.deleteTag");
		Route::post('{resource}/{code}/tags/add', [ResourceController::class, 'addTag'])->middleware(['hashids:code'])->name("resource.addTag");
		Route::get('{resource}/{code}/edit', [ResourceController::class, 'edit'])->middleware(['hashids:code'])->name("resource.edit");
		Route::delete('{resource}/{code}/destroy', [ResourceController::class, 'destroy'])->middleware(['hashids:code'])->name("resource.destroy");
		Route::post('{resource}/{code}/before-destroy', [ResourceController::class, 'beforeDestroy'])->middleware(['hashids:code'])->name("resource.before_destroy");
		Route::delete('{resource}/{id}/field/destroy', [ResourceController::class, 'destroyField'])->name("resource.field.destroy");
		Route::post('inputs/option_list', [ResourceController::class, 'option_list'])->name("resource.inputs.option_list");
		Route::post('globalsearch', [ResourceController::class, 'globalSearch'])->name("resource.globalsearch");
		Route::get('{resource}', [ResourceController::class, 'index'])->name("resource.index");
	});
});


Route::group(['prefix' => "api"], function () {
	Route::post('login', [ResourceController::class, 'apiLogin']);
	Route::group(['middleware' => ['api.vstack_jwt']], function () {
		Route::get('{resource_id}', [ResourceController::class, 'getResource']);
		Route::post('{resource_id}/create', [ResourceController::class, 'createResource']);
		Route::get('{resource_id}/{code}', [ResourceController::class, 'findByCode']);
		Route::put('{resource_id}/{code}/edit', [ResourceController::class, 'editResource']);
		Route::delete('{resource_id}/{code}/destroy', [ResourceController::class, 'destroyResource']);
	});
});
