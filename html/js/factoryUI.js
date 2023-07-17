function factoryUI(cfg) {

    switch (cfg.view) {

        case "window":
            return windowUI(cfg);
            break;

        default:
            return simpleUI(cfg);
            break;

    }

}


function windowUI(cfg) {

    const d = new Dialog(cfg.id ? cfg.id : 'confirm');


    d.clearHeader();
    if (cfg.hasOwnProperty('header')) {
        cfg.header.forEach((v) => {
            d.setHeader(factoryUI(v))
        })
    }

    d.clearBody();
    if (cfg.hasOwnProperty('body')) {
        cfg.body.forEach((v) => {
            d.setBody(factoryUI(v))
        })
    }

    d.clearFooter();
    if (cfg.hasOwnProperty('footer')) {
        cfg.footer.forEach((v) => {
            d.setFooter(factoryUI(v))
        })
    }
    d.initFooter();

    if (cfg.hasOwnProperty('on')) {
        if(cfg.on.hasOwnProperty('onShow')){
            d.onShow = cfg.on.onShow;
        }
        if(cfg.on.hasOwnProperty('onHide')){
            d.onHide = cfg.on.onHide;
        }
        if(cfg.on.hasOwnProperty('commit')){
            d.commit = cfg.on.commit;
        }
    }

    return d;

}




function  simpleUI(cfg){

    const el = document.createElement(cfg.view);
    for(const key in cfg){

        if(key!='view')
            el[key] = cfg[key];
    }
    return el;
}


class Dialog {

    constructor(id = 'confirm') {

        if (Dialog.instance) {
            return Dialog.instance;
        }

        let element = document.getElementById(`#${id}`);
        if (element !== null) {
            console.error("container not exists");
        }

        this.container = $(`#${id}`);
        this.footer = this.container.find('.modal-footer')[0];
        this.body = this.container.find('.modal-body')[0];
        this.header = this.container.find('.modal-title')[0];
        this.container.on('show.bs.modal', () => this.onShow());
        this.container.on('hide.bs.modal', () => this.onHide());

        Dialog.instance = this;
    }

    onShow() {
    }

    onHide() {
    }

    clearHeader(){
        this.header.innerHTML = '';
    }
    setHeader(view) {
        this.header.appendChild(view);
        return this;
    }

    clearBody(){
        this.body.innerHTML = '';
    }
    setBody(view) {
        this.body.appendChild(view);
        return this;
    }


    clearFooter(){
        this.footer.innerHTML = '';
    }
    setFooter(view) {
        this.footer.appendChild(view);
        return this;
    }

    initFooter() {
        this.commitBtn();
        this.closeBtn();
    }

    closeBtn() {
        const button = document.createElement('button');
        button.innerHTML = 'No';
        button.className = "btn btn-secondary";
        button.onclick = () => this.close();
        this.footer.appendChild(button);
    }

    commitBtn() {
        const button = document.createElement('button');
        button.innerHTML = 'Yes I am';
        button.className = "btn btn-primary";
        button.onclick = () => this.onCommit();
        this.footer.appendChild(button);
    }

    onCommit() {
        this.commit();
        this.container.modal('hide')
    }

    commit() {
    }

    onClose() {
        this.close()
    }

    open() {
        this.container.modal('show');
        return this;
    }

    close() {
        this.container.modal('hide');
    }
}