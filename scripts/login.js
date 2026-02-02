const base_url = "http://cop4331-team21.online"

const button = document.getElementById("sign-in");

button.addEventListener("click", (e) => {
    e.preventDefault();

    const form = document.getElementById("login-form");

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    sign_in(email, password);
});

async function sign_in(email, password) {

    try {

        const res = await fetch(base_url + "/api/login.php", {

            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                login: email,
                password: password
            })

        });

        const data = await res.json();

        if (!res.ok) {
            throw new Error("Login Failed: " + data.error);
        }

        console.log(data);
    } catch (err) {
        console.error(err);
    }
}
