import store from '../vue/client';

export const loading = {
    true() {
        store.commit('loading', true);
    },
    false() {
        store.commit('loading', false);
    }
}

