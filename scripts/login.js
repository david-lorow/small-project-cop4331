const base_url = "http://cop4331-team21.online"

const button = document.getElementById("sign-in");

button.addEventListener("click", async function(e) {
    e.preventDefault();

    const form = document.getElementById("login-form");

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    const user = await sign_in(email, password);
     
    saveCookie(user.FirstName, user.LastName, user.id);
    
    window.location.replace("../contacts/");

});

function saveCookie(firstName, lastName, userId)
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString() + ";path=/";
}


async function sign_in(email, password) {

    try {

        const res = await fetch(base_url + "/api/login.php", {

            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                login: email,
                Password: password
            })

        });

        const data = await res.json();

        if (!res.ok) {
            throw new Error("Login Failed: " + data.error);
        }

	return data;

    } catch (err) {
        console.error(err);
    }
}
