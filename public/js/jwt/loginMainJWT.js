const { createApp } = Vue;
const element = document.getElementById("login");
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
                    Swal.fire({
                        position: "top-end",
                        icon: "error",
                        title: err.response.data.message,
                        showConfirmButton: false,
                        timer: 1500,
                    });
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
                            Swal.fire({
                                position: "top-end",
                                icon: "success",
                                title: res.data.message,
                                showConfirmButton: false,
                                timer: 1500,
                            }).then((res) => {
                                localStorage.setItem("authData", JSON.stringify(userData));
                            });
                        }
                    })
                    .catch((err) => {
                        if (err.ERR_BAD_RESPONSE) {
                            Swal.fire({
                                position: "top-end",
                                icon: "error",
                                title: err.response.data.message,
                                showConfirmButton: false,
                                timer: 1500,
                            }).then((res) => {
                                localStorage.removeItem("authData");
                            });
                        }
                    });
            }
        },
    },
    mounted() {},
});

if (element) {
    app.mount("#" + element.id);
}