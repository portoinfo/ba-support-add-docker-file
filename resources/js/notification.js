// constante que armazena as funções
const notification = {};
// função de disparo de notificação
notification.send = (event) => {
    // O título da notificação
    let title = event.notification.title;
    // As opções da notificação
    let options = {
            // O corpo(mensagem) da notificação.
            body: event.notification.body,
            // A URL da imagem usada como um ícone da notificação.
            icon: event.notification.icon,
        }
        // A url para onde a notificação irá redirecionar
    let url = event.notification.url !== '' ? event.notification.url : false;

    // Verifica se o browser suporta notificações
    if (!("Notification" in window)) {
        console.log("Este browser não suporta notificações de Desktop");
    }

    // Let's check whether notification permissions have already been granted
    else if (Notification.permission === "granted") {
        // If it's okay let's create a notification
        let n = new Notification(title, options);
        if (url) {
            n.onclick = function(event) {
                event.preventDefault(); // prevent the browser from focusing the Notification's tab
                window.open(url);
            }
        }
    }

    // Otherwise, we need to ask the user for permission
    else if (Notification.permission !== 'denied') {
        Notification.requestPermission(function(permission) {
            // If the user accepts, let's create a notification
            if (permission === "granted") {
                let n = new Notification(title, options);
                if (url) {
                    n.onclick = function(event) {
                        event.preventDefault(); // prevent the browser from focusing the Notification's tab
                        window.open(url);
                    }
                }
            }
        });
    }
};

notification.chat = (event) => {
    switch (event.status) {
        case 'OPENED':
            notification.send(event);
            break;
        case 'IN_PROGRESS':

            break;
        case 'TRANSFERRED_TO_AGENT':

            break;
        case 'TRANSFERRED_TO_DEPARTMENT':
            notification.send(event);
            break;
        case 'CLOSED':

            break;
        case 'RESOLVED':

            break;
        case 'CANCELED':

            break;
    };

    notification.send(event);
};

notification.ticket = (event) => {};

notification.connect = ($companyId, $user) => {
    Echo.join(`notification.${$companyId}`).listen("GlobalNotification", (event) => {
        if ($user.departments_id.includes(event.notification.company_department_id)) {
            console.log('department');
            if (event.notification.type === 'chat') {
                console.log('chat');
                notification.chat(event);
            } else if (event.notification.type === 'ticket') {
                notification.ticket(event);
            }
        }
    });
};

export { notification };