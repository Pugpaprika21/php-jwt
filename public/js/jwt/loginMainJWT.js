const { createApp } = Vue;

createApp({
    data() {
        return {
            url: "http://localhost:8080/jwt/",
            login: {
                username: "",
                password: "",
            },
        };
    },
    methods: {
        loginJWT(e) {
            e.preventDefault();
            let loginParams = {
                username: this.login.username,
                password: this.login.password,
            };
            axios
                .post(this.url + "login", loginParams)
                .then((res) => {
                    if (res.status == 200) {
                        console.log(res.data);
                    }

                })
                .catch((err) => {
                    console.error(err);
                });
        },
    },

    mounted() {

    },

}).mount("#app");