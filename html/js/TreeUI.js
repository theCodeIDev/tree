class TreeUI {

    constructor(tree) {
        this.tree = tree;
        this.render();
    }

    render() {

        this.renderDom();

        const treeContainer = document.getElementById('tree');
        treeContainer.innerHTML = '';
        treeContainer.appendChild(this.rootElement);
    }


    renderDom() {
        this.rootElement = document.createElement('ul');
        this.tree.data.forEach(e => this.buildNode(e, this.rootElement));
    }


    li(nodeData, parentElement) {
        const {id, hidden} = nodeData;
        const li = document.createElement('li');
        li.id = id;
        li.style.display = !hidden ? 'block' : 'none';
        parentElement.appendChild(li);
        return li;
    }

    header(nodeData, li) {

        const {id, name, hidden, data} = nodeData;
        const header = document.createElement('div');

        if (data.length > 0) {
            header.appendChild(this.expandButton(data));
        }

        header.classList.add('header');

        const span = document.createElement('span');
        span.className = "node-caption";
        span.innerText = name;
        header.appendChild(span);
        header.appendChild(this.deleteButton(li));
        header.appendChild(this.addButton(li));

        return header;
    }

    expandButton(data) {

        const button = document.createElement('button');
        button.className = 'btn btn-link';
        button.innerHTML = data.some(obj => obj.hidden) ?
            '<i class="fa fa-chevron-right"></i>' : '<i class="fa fa-chevron-down"></i>';

        button.addEventListener('click', () => {
            data.forEach((e, i) => {
                data[i].hidden = !data[i].hidden;
            })
            this.render();
        });

        return button;
    }

    deleteButton(li) {
        const button = document.createElement('button');
        button.innerHTML = `<button type="button" class="btn btn-danger btn-circle"><i class="fa fa-minus-circle fa-lg"></i>`
        button.addEventListener('click', () => {
            this.confirmDelete(li.id)
        });
        return button;
    }

    addButton(li) {

        const button = document.createElement('button');

        button.innerHTML = `<button type="button" class="btn btn-success btn-circle"><i class="fa fa-plus-circle  fa-lg"></i>`
        button.addEventListener('click', () => {

            const {newNode, parentNode} = this.tree.prepare(li.id);


            apiImpl.call({
                m: "TreeController",
                p: {
                    action: "saveNode",
                    data: newNode.getProps()
                }
            }, (d) => {

                if (d.status && d.status == "ok") {
                    parentNode.addChild(newNode);
                    this.render();
                }

            }, (err) => {
                console.log(err);
            });


        });
        return button;
    }

    content(nodeData) {
        let {id, name, hidden, data} = nodeData;
        const content = document.createElement('ul');
        content.classList.add('content');
        return content;
    }

    buildNode(nodeData, parentElement) {

        const {hidden, data} = nodeData;
        const _li = this.li(nodeData, parentElement)
        const _content = this.content(nodeData);
        _li.appendChild(this.header(nodeData, _li));
        _li.appendChild(_content);

        if (!hidden) {
            data.forEach(childData => this.buildNode(childData, _content));
        }
    }

    parse(data) {
        this.tree.parse(data);
        this.render();
    }

    confirmDelete(lid) {

        const timer = new Timer(18);

        const cfg = {
            view: 'window',
            id: 'confirm',
            header: [{
                view: 'span',
                innerText: 'Delete confirmation'
            }],
            body: [{
                view: 'span',
                innerText: 'This is very dangerous, you shouldn`t do it! Are you really really shure?'
            }],
            footer: [{
                view: "div",
                style: "flex: auto;color:red;",
                id: "TimerCount",
                innerText: ''
            }],
            on: {
                onShow: () => {
                    timer.start((count) => {
                        const e = document.getElementById('TimerCount');
                        e.innerText = count;
                        if (count === 1) $('#confirm').modal('hide');
                    })
                },
                onHide: () => {
                    timer.stop();
                },
                commit: () => {
                    this.delete(lid)
                },
            }
        };

        factoryUI(cfg).open();

    }

    delete(lid){


        apiImpl.call({
            m: "TreeController",
            p: {
                action: "deleteNodes",
                parent_id:lid
            }
        }, (d) => {

            if (d.status && d.status == "ok") {

                this.tree.deleteNodeById(lid)
                this.render();

            }

        }, (err) => {
            console.log(err);
        });
    }

    loadTree() {
        apiImpl.call({
            m: "TreeController",
            p: {
                action: "loadTree",
            }
        }, (d) => {

            if (d.status && d.status == "ok" && d.data.length>0) {
                this.parse(d.data);
                this.tree.setId(parseInt(d.id));
            }

        }, (err) => {
            console.log(err);
        });
    }

}