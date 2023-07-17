class Node {
    constructor(id = "root", name = "root", hidden = false, parent_id = "root") {
        this.id = id;
        this.name = name;
        this.hidden = hidden;
        this.parent_id = parent_id;
        this.data = [];
    }

    addChild(node) {
        this.data.push(node);
        return node;
    }

    removeChild(node) {
        const index = this.data.indexOf(node);
        if (index > -1) {
            this.data.splice(index, 1);
        }
    }

    getProps() {
        return {
            id: this.id,
            parent_id: this.parent_id,
            name: this.name,
            hidden: this.hidden
        }
    }

}