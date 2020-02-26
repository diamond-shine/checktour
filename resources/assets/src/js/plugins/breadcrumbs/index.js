export class Breadcrumbs {
    constructor(manager, handler, args) {
        this.manager = manager;
        this.handler = handler;

        this.crumbs = [];

        this.args = args;
    }

    push(title, route) {
        this.crumbs.push({
            title,
            route: typeof route === 'string' ?
                { name: route } :
                route
        });
    }

    parent(name, ...args) {
        if (!this.manager.has(name)) {
            console.error(`Invalid parent name. Breadcrumbs with name [${name}] not found`);
            return;
        }

        this.crumbs = [
            ...this.manager.make(name, ...args)().build(),
            ...this.crumbs
        ];
    }

    build() {
        this.handler(this, ...this.args);

        return this.crumbs;
    }
}

class Manager {
    constructor() {
        this.allBreadcrumbs = {};
    }

    register(name, handler) {
        this.allBreadcrumbs[ name ] = handler;
    }

    has(name) {
        return this.allBreadcrumbs.hasOwnProperty(name);
    }

    create(name, ...args) {
        if (!this.has(name)) {
            console.error(`Breadcrumbs with name [${name}] not found`);
            return;
        }

        return new Breadcrumbs(
            this,
            this.allBreadcrumbs[ name ],
            args
        );
    }

    make(name, args) {
        return () => this.create(name, args);
    }

    generate(name, args) {
        return this.create(name, args);
    }
};

export default new Manager;
