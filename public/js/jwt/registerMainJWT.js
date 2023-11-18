const { createApp } = Vue;
const element = document.getElementById("register");
const app = createApp({
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
        registerJWT(e) {
            e.preventDefault();
            let loginParams = {
                username: this.login.username,
                password: this.login.password,
            };
            axios
                .post(this.url + "register", loginParams)
                .then((res) => {
                    if (res.status == 200) {
                        console.log(res.data);
                    }
                })
                .catch((err) => {
                    if (err.status == 500) {
                        console.error(err);
                    }
                });
        },
    },
    mounted() {},
});

if (element) {
    app.mount("#" + element.id);
}