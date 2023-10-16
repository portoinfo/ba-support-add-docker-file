import store from '../vue/client';

export const formatEmail = function(email) {
    if (email) {
        if (store.state.user.is_anonymous) {
            return this.$t('bs-anonymous')
        } else {
            if (hasWhiteLabelPrefix(email)) {
                email = sanitizeWhiteLabelPrefix(email);
            }

            return email;
        }
    }
};

function hasWhiteLabelPrefix(email) {
    email = email.toUpperCase();

    if(!email.startsWith('PREFIX_WL_')) {
      return false
    }

    return true;
}

function sanitizeWhiteLabelPrefix(email) {
    email = email.toUpperCase();
    email = email.replace('PREFIX_WL_', '').replace(/[^\_]+_/, '');
    return email.toLowerCase();
}
