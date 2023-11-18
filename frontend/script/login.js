import { validateToken } from "./lib/account.js";
import { apiCall } from "./lib/api.js";

const checkbox = document.getElementById('loginORsignin');
const login = document.getElementById('lbl-login');
const signup = document.getElementById("lbl-signin");
const form = document.getElementsByClassName("form");
const submit = document.getElementById("submit");

const username = document.getElementById("email");
const password = document.getElementById("password");

const logout = document.getElementById("logout");

let boolLogin = true;

checkbox.addEventListener('change', function () {
    if (!checkbox.checked) {
        boolLogin = true;
        login.style.color = 'var(--text)';
        login.style.background = 'linear-gradient(-45deg, var(--accent-grad-1), var(--accent-grad-2))';
        signup.style.color = 'var(--accent)';
        signup.style.background = 'unset';
        submit.value = "Login";
    } else {
        login.style.color = 'var(--accent)';
        login.style.background = 'unset';
        signup.style.color = 'var(--text)';
        signup.style.background = 'linear-gradient(-45deg, var(--accent-grad-1), var(--accent-grad-2))';
        submit.value = "Sign up";
        boolLogin = false;
    }
});

const ErrorMessageHolder = document.getElementById('wrong-pw');

function errorUser() {
    ErrorMessageHolder.style.opacity = 1;
    if (document.getElementById("email").value == "" || document.getElementById("password").value == "") {
        ErrorMessageHolder.innerHTML = "Please enter email or password";
    }
    else {
        if (boolLogin) {
            ErrorMessageHolder.innerHTML = "email or password is wrong!";
        }
    }
}

submit.onclick = () => {
    if (checkbox.checked) {
        apiCall("/api/user/create", [
            "username=" + username.value,
            "password=" + password.value
        ]).then(res => {
            localStorage.setItem("token", res.token);
            location.reload();
        });
    } else {
        apiCall("/api/user/login", [
            "username=" + username.value,
            "password=" + password.value
        ]).then(res => {
            localStorage.setItem("token", res.token);
            location.reload();
        });
    }
}

logout.onclick = async () => {
    apiCall("/api/user/logout", [], true, await validateToken()).then(res => {
        localStorage.removeItem("token");
        location.reload();
    });
}

validateToken().then(token => {
    if (token) {
        document.getElementById("notLoggedIn").style = "display: none";
    } else {
        document.getElementById("loggedIn").style = "display: none";
    }
})