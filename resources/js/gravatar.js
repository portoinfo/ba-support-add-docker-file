export const gravatar = {
    get(email) {
        axios.get("gravatar", {
            params: {
                email: email
            }
        }).then(({ data }) => {
            return data;
        });
    }
}