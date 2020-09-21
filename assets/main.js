document.addEventListener('DOMContentLoaded', () => {
    class MainModal {
        constructor(modalId){
            this.modal           = document.getElementById(modalId);
            this.modalTitleClass = '.modal-title';
            this.modalBodyClass  = '.card-body';
            this.action          = frontend.action;
        }
        render(content){
            if (typeof content.userDetail !== 'undefined' && !this.isEmpty(content.userDetail)){
                this.modal.addEventListener('shown.bs.modal', (event) => {
                    let instance = event.target;
                    let {name, username, email, phone, website} = content.userDetail;
                    let body = `<div>
                    <p>Username: <strong>${username}</strong></p>
                    <p>Email: <strong>${email}</strong></p>
                    <p>Phone: <strong>${phone}</strong></p>
                    <p>Website: <strong>${website}</strong></p>
                    </div>`;
                    this.updateContent(instance, '<strong>' + name + '</strong>', body)
                });
                this.modal.addEventListener('hidden.bs.modal', (event) => {
                    let instance = event.target;
                    this.updateContent(instance, '', '');
                });
            }
        }
        updateContent(instance, title, content){
            instance.querySelector(this.modalTitleClass).innerHTML = title;
            instance.querySelector(this.modalBodyClass).innerHTML = content;
        }
        ajaxRequest(userId){
            (async () => {
                let formData = new FormData();
                formData.append('action', this.action);
                formData.append(frontend.userId, userId);
                formData.append(frontend.nonceKey, frontend.nonce);
                let rawResponse = await fetch(frontend.ajaxurl, {
                    method: 'POST',
                    body: formData
                });
                let content = await rawResponse.json();
                this.render(content);
            })();
        }
        run(){
            this.modal.addEventListener('show.bs.modal', (event) => {
                let targetElement = event.relatedTarget;
                if (targetElement) {
                    let userId = targetElement.dataset.user_id;
                    let instance = event.target;
                    this.updateContent(instance, '...load', '...load')
                    this.ajaxRequest(userId);
                }
            });
        }
        isEmpty(obj){
            return Object.keys(obj).length === 0 && obj.constructor === Object
        }
    }
    (new MainModal('userModal')).run();
});



