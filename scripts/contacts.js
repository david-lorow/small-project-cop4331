const base_url = "http://cop4331-team21.online"

const button = document.getElementById("logOut");

let contactStorage = [];

document.addEventListener("DOMContentLoaded", function() {
    const welcome = document.getElementById("userWelcome");

    readCookie();

    if(typeof firstName !== 'undefined' && firstName){
        welcome.textContent = `Hello, ${firstName}!`;
    }
    else{
        welcome.textContent = "Hello, User!";
    }
});

// Leinecker's code
function saveCookie()
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));
    const expiry = ";expires=" + date.toGMTString() + ";path=/";

	document.cookie = "firstName=" + firstName + expiry;
    document.cookie = "lastName=" + lastName + expiry;
    document.cookie = "userId=" + userId + expiry;
}

function readCookie()
{
	userId = -1;
	let data = document.cookie;
	let splits = data.split(";");
	for(var i = 0; i < splits.length; i++) 
	{
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		if( tokens[0] == "firstName" )
		{
			firstName = tokens[1];
		}
		else if( tokens[0] == "lastName" )
		{
			lastName = tokens[1];
		}
		else if( tokens[0] == "userId" )
		{
			userId = parseInt( tokens[1].trim() );
		}
	}
	
	if( userId < 0 )
	{
		window.location.href = "../index.html";
	}
	else
	{
    	document.getElementById("userName").innerHTML = "Logged in as " + firstName + " " + lastName;
	}
}

async function goAddContact(){

    let resultTable = document.getElementById("contactResultTable");
    let addContact = document.getElementById("addContactUI");

    if(!resultTable.classList.contains("hidden")){
        resultTable.classList.add("hidden");
        addContact.classList.remove("hidden");
    }
    else{
        resultTable.classList.remove("hidden");
        addContact.classList.add("hidden");
    }
}

function showTable(){
    let resultTable = document.getElementById("contactResultTable");
    let addContact = document.getElementById("addContactUI");

    resultTable.classList.remove("hidden");
    addContact.classList.add("hidden");
}

async function addContact(){
    let FirstName = document.getElementById("firstName").value;
    let LastName = document.getElementById("lastName").value;
    let Phone = document.getElementById("phone").value;
    let Email = document.getElementById("email").value;

    const contactAdded = document.getElementById("contactAdded");
    contactAdded.innerHTML = "";

    let tmp = {FirstName:FirstName, LastName:LastName, Phone:Phone, Email:Email};
    let jsonPayload = JSON.stringify(tmp);

    const url = base_url + '/api/create_contact.php';

    let xml = new XMLHttpRequest();
    xml.open("POST", url, true);
    xml.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    xml.withCredientials = true;

    try{
        xml.onreadystatechange = function(){
            if(this.readyState == 4 && this.status == 200){
                contactAdded.innerHTML = "Contact Added Successfully!";
                contactAdded.classList.remove("hidden");
            }
        };
        xml.send(jsonPayload);
    }
    catch(err){
        contactAdded.innerHTML = "Error adding contact: " + err.message;
        contactAdded.classList.remove("hidden");
    }

}

async function searchContact(){
    const fullSearch = document.getElementById("searchContacts").value.trim();

    const FirstName = fullSearch.split(" ")[0] || "";
    const LastName = fullSearch.split(" ")[1] || "";

    try{
        const res = await fetch(base_url + "/api/get_contacts_with_name.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "include",
            body: JSON.stringify({FirstName, LastName})
        });

        const data = await res.json();

        if(res.ok && data.results){
            contactStorage = data.results;
            displayResults(contactStorage);
        }
        else{
            contactStorage = [];
            let errorMessage = document.getElementById("contactResultTable");
            errorMessage.innerHTML = "<tr><td colspan='5' style='color: red; text-align: center; font-weight: bold;'>No contacts found.</td></tr>";
        }
    }
    catch(error){
        console.error("Error searching contacts: " + error);
    }
}

function displayResults(contacts){
    const resultTable = document.getElementById("contactResultTable");
    resultTable.innerHTML = ""; // default blank

    contacts.forEach(contact => { // contacts or contactArray??
        const row = document.createElement("tr");
        row.setAttribute("id", `row-${contact.ID}`);
        row.innerHTML = getRowTemp(contact);
        resultTable.appendChild(row);
    });
}

function getRowTemp(contact){
    return `
            <td>${contact.FirstName}</td>
            <td>${contact.LastName}</td>
            <td>${contact.Phone}</td>
            <td>${contact.Email}</td>
            <td>
            <button type="button" class=inlineButton onclick="editContact(${contact.ID})" title="Edit"><img src="../images/editLogo.png" alt="Edit" style="width: 24px; height: 24px;"></button>
            <button type="button" class=inlineButton onclick="deleteContact(${contact.ID})" title="Delete"><img src="../images/deleteLogo.png" alt="Delete" style="width: 24px; height: 24px;"></button>
            </td>
        `;
}

function editContact(contactId){
    const contact = contactStorage.find(c => c.ID === contactId);
    if(!contact){
        return;
    }

    const row = document.getElementById(`row-${contactId}`);

    row.innerHTML = `
        <td><input type="text" id="editFirstName-${contactId}" value="${contact.FirstName.replace(/"/g, '&quot;')}"></td>
        <td><input type="text" id="editLastName-${contactId}" value="${contact.LastName.replace(/"/g, '&quot;')}"></td>
        <td><input type="text" id="editPhone-${contactId}" value="${contact.Phone}"></td>
        <td><input type="text" id="editEmail-${contactId}" value="${contact.Email}"></td>
        <td>
            <button type="button" class=inlineButton onclick="saveContact(${contactId})" title="Save"><img src="../images/saveLogo.png" alt="Save" style="width: 24px; height: 24px;"></button>
            <button type="button" class=inlineButton onclick="cancelEdit(${contactId})" title="Cancel"><img src="../images/cancelLogo.png" alt="Cancel" style="width: 24px; height: 24px;"></button>
        </td>
    `;
}

async function saveContact(contactId){
    const updatedContact = {
        ID: contactId,
        FirstName: document.getElementById(`editFirstName-${contactId}`).value.trim(),
        LastName: document.getElementById(`editLastName-${contactId}`).value.trim(),
        Phone: document.getElementById(`editPhone-${contactId}`).value.trim(),
        Email: document.getElementById(`editEmail-${contactId}`).value.trim()
    };
    
    try{
        const res = await fetch(base_url + "/api/update_contact.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "include",
            body: JSON.stringify(updatedContact)
        });

        const data = await res.json();

        if(res.ok && data.ID){
            const index = contactStorage.findIndex(c => c.ID === contactId);
            if(index !== -1){
                contactStorage[index] = data; // update local storage with new contact info
            }

            const row = document.getElementById(`row-${contactId}`);
            row.innerHTML = getRowTemp(data);
        }
        else{
            alert("Error: " + (data.error || "Failed to update contact information."));
        }

    }
    catch(error){
        console.error("Error updating contact: " + error);
    }
}

function cancelEdit(contactId){
    const contact = contactStorage.find(c => c.ID === contactId);
    if(!contact){
        return;
    }

    const row = document.getElementById(`row-${contactId}`);
    row.innerHTML = getRowTemp(contact); // back to original contact info
}

async function deleteContact(contactId){
    const contact = contactStorage.find(c => c.ID === contactId);
    if(!contact){
        return;
    }

    const deleteConfirm = confirm(`Are you sure you want to delete ${contact.FirstName} ${contact.Lastname}?`);
    if(!deleteConfirm){
        return;
    }

    try{
        const res = await fetch(base_url + "/api/delete_contact.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "include",
            body: JSON.stringify({ID:contactId})
        });

        const data = await res.json();

        if(res.ok && !data.error){
            searchContact(); // refresh contact table
        }


    }
    catch(error){
        console.error("Error deleting contact: " + error);
    }
}

button.addEventListener("click", (e) => {
    e.preventDefault();
    logOut();
});

async function logOut(){
	
    try{

        const res = await fetch(base_url + "/api/logout.php", {

            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "include",
            body: JSON.stringify({})
        });

        const data = await res.json();

        if (res.ok && data.success) {
            console.log("Logout successful");
            localStorage.clear();
            window.location.href = "index.html";
        }
        else{
            console.error("Logout failed: " + data.error);
        }

    }
    catch(error){
        console.error("Error during logout: ", error);
    }
}