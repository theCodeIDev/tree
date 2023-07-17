class Tree {

    constructor() {
        this.id = 0;
        this.node = Node;
        this.data = [new this.node()];
    }

    addNode(id) {
        const o = this.prepare(id)
        const newNode = o.parentNode.addChild(o.newNode);
        return newNode;
    }

    prepare(id) {
        const n = this.findNodeById(id);
        if (n === null) return;
        const node = new Node(++this.id, `item-${this.id}`, false, n.id);
        return {
            newNode: node,
            parentNode: n
        };
    }


    setId(id) {
        this.id = id;
    }


    findNodeById(_id) {
        let ret = null;
        const fnd = (items) => {

            items.data.forEach((childNode) => {

                if (childNode.id == _id) {
                    ret = childNode;
                } else {
                    fnd(childNode);
                }

            });
        }
        fnd(this);

        return ret;

    }

    parse(dataArray) {

        const createNodes = (data) => {
            return data.map((item) => {
                const node = new this.node(item.id, item.name, item.hidden, item.parent_id);
                if (item.data && item.data.length > 0) {
                    node.data = createNodes(item.data);
                }
                return node;
            });
        };

        this.findNodeById('root').data = createNodes(dataArray);
    }

    deleteNodeById(_id) {

        if (_id == "root") {
            this.findNodeById('root').data = [];
            return;
        }

        const parentNode = this.findNodeById(this.findNodeById(_id).parent_id);

        if (parentNode === null) return;
        parentNode.data = parentNode.data.filter(obj => obj.id != _id);
    }


}