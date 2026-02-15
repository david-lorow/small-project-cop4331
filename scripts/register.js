const base_url = "http://cop4331-team21.online"

const button = document.getElementById("register-button");

button.addEventListener("click", async function(e) {
    e.preventDefault();
    const form = document.getElementById("register-form");

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const first_name = document.getElementById("first-name").value.trim();
    const last_name = document.getElementById("last-name").value.trim();

    const user = await sign_up(first_name, last_name, email, password)
    
    saveCookie(user.firstName, user.lastName, user.id);

    window.location.replace("../contacts/");
});

function saveCookie(firstName, lastName, userId)
{
        let minutes = 20;
        let date = new Date();
        date.setTime(date.getTime()+(minutes*60*1000));
        document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString() + ";path=/";
}


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
            throw Error("Error registering user: " + data.error)
        }

        return data;

    } catch (err) {
        console.error(err)
    }
}
