import store from './store';
/**
 * Recebe a hash_id de um usuário e retorna o status dele (online, busy ou offline);
 */
export const status = {
    get(id) {
        let online_index = store.state.online_users.findIndex((item) => item.id === id || item.hash_id === id);

        if (online_index !== -1) {
            return store.state.online_users[online_index].status;
        } else {
            return "offline";
        }
    }
}