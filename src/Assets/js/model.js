import { Model as BaseModel } from 'vue-api-query'

export default class Model extends BaseModel {
    // Define a base url for a REST API
    baseURL() {
        return '/vstack/query-builder'
    }

    // Implement a default request method
    request(config) {
        config.url = this.makeUrl();
        return this.$http.request(config)
    }

    resource() {
        return 'App\\Http\\Models\\MyModel';
    }

    makeUrl() {
        const model = this.resource().replaceAll("\\","-");
        return `${this.baseURL()}/${model}`
    }

    // Define the primary key of the model
    primaryKey() {
        return 'id'
    }
}
