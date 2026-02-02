const base_url = "http://cop4331-team21.online"

const button = document.getElementById("register-button");

button.addEventListener("click", () => {
    const form = document.getElementById("register-form");

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const first_name = document.getElementById("first-name").value.trim();
    const last_name = document.getElementById("last-name").value.trim();

    sign_up(first_name, last_name, email, password);

});


async function sign_up(first_name, last_name, email, password) {
    try {

        const res = await fetch(base_url + "/api/register.php", {

            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                FirstName: first_name,
                LastName: last_name,
                email: email,
                Password: password
            })

        });

        const data = await res.json();

        if (!res.ok) {
            throw Error("Error registering user: " + data.error);
        }

        console.log(data);

    } catch (err) {
        console.error(err);
    }
}