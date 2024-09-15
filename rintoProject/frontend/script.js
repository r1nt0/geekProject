document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    if (loginForm) {
        loginForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            await handleLogin(email, password);
        });
    }

    

    if (signupForm) {
        signupForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const email = document.getElementById('signupEmail').value;
            const password = document.getElementById('signupPassword').value;
            const phone = document.getElementById('phone').value;
            const name = document.getElementById('name').value;
            const gender = document.getElementById('gender').value;
            await handleSignup(email, password, phone, name, gender);
        });
    }
});

const baseUrl = "http://localhost";

async function handleLogin(email, password) {
    try {
        const response = await fetch(`${baseUrl}/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        if (response.ok) {
            const data = await response.json();
           
            //console.log(data);
            // const rawText = await response.text();
            // console.log('Raw response text:', rawText);
            sessionStorage.setItem('token', data.message);
            if(data.value === true){
                //alert('login successfull');
                if(data.userType === 0){
                    window.location.href = "./adminPage.html";
                }else{
                    window.location.href = "./userPage.html";
                }
            }else if (data.value === false){
                alert(data.message);
            }else{
                alert('wrong');
            }
            // Redirect to home or another page
            // window.location.href = '/home';
        } else {
            const error = await response.json();
            alert(`Error: ${error.message}`);
        }
    } catch (error) {
        alert('An unexpected error occurred.');
    }
}

async function handleSignup(email, password, phone, name, gender) {
    try {
        const response = await fetch(`${baseUrl}/signup`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password, phone, name, gender })
        });

        if (response.ok) {
            
            const data = await response.json();
            sessionStorage.setItem('token', data.message);
            if(data.value === true){
            alert(data.message);
            // Redirect to login page
            window.location.href = './userPage.html';
            }else{
                alert(data.message);
            }
        } else {
            const error = await response.json();
            alert(`Error: ${error.message}`);
        }
    } catch (error) {
        alert('An unexpected error occurred.');
    }
}
