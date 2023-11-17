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
                        let userData = {
                            username: res.data.username,
                            password: res.data.password,
                            tokenJWT: res.data.tokenJWT,
                        };
                        this.setAuthLogin(userData);
                    }
                })
                .catch((err) => {
                    localStorage.removeItem("authData");
                    console.error(err);
                });
        },
        setAuthLogin(userData) {
            if (Object.keys(userData).length > 0) {
                axios
                    .post(this.url + "check-login", userData, {
                        headers: {
                            Authorization: "Bearer " + userData.tokenJWT,
                            "Content-Type": "application/json",
                        },
                    })
                    .then((res) => {
                        if (res.status == 200) {
                            console.log(res.data);
                        }

                    })
                    .catch((err) => {
                        console.error(err);
                    });

                localStorage.setItem("authData", JSON.stringify(userData));
            }
        },
    },
    mounted() {},
}).mount("#app");