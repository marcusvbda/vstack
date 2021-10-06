import resource from "./modules/resource.module";

export default function() {
    const store = {
        modules: {
            resource
        }
    };
    return store;
}
