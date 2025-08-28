// --- Form toggle ---
function showForgot(){document.getElementById('loginForm').classList.remove('active');document.getElementById('registerForm').classList.remove('active');document.getElementById('forgotForm').classList.add('active');}
function showLogin(){document.getElementById('forgotForm').classList.remove('active');document.getElementById('registerForm').classList.remove('active');document.getElementById('loginForm').classList.add('active');}
function showRegister(){document.getElementById('loginForm').classList.remove('active');document.getElementById('forgotForm').classList.remove('active');document.getElementById('registerForm').classList.add('active');}

// --- Toggle password ---
function togglePassword(toggleId,inputId){
    const toggle=document.getElementById(toggleId);
    const input=document.getElementById(inputId);
    toggle.addEventListener('click',()=>{
        input.type = input.type === "password" ? "text" : "password";
        toggle.classList.toggle("fa-eye");
        toggle.classList.toggle("fa-eye-slash");
    });
}
togglePassword("toggle-login-password","login-password");
togglePassword("toggle-reg-password","reg-password");

// --- Show error under field & highlight input ---
function showError(input,message){
    let errorDiv = input.parentElement.querySelector('.form-error');
    errorDiv.textContent = message;
    if(message) input.classList.add('input-error');
    else input.classList.remove('input-error');
}

// --- Regex ---
const nameRegex = /^[a-zA-Z\s]+$/;
const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/i;

// --- Inputs ---
const fullNameInput = document.querySelector('input[name="full_name"]');
const companyInput = document.querySelector('input[name="company_name"]');
const emailInput = document.querySelector('input[name="email"]');
const passwordInput = document.querySelector('#reg-password');

// --- Real-time Name check ---
fullNameInput.addEventListener('input',()=>{
    if(!nameRegex.test(fullNameInput.value)){
        showError(fullNameInput,'Invalid name');
        return;
    }
    fetch('../controller/check_name.php', {
        method: 'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'full_name='+encodeURIComponent(fullNameInput.value)
    }).then(r=>r.text()).then(resp=>{
        if(resp==='exists'){
            showError(fullNameInput,'Name already exists.');
            // Add Continue button
            if(!document.querySelector('#continue-name')){
                const btn = document.createElement('button');
                btn.type='button';
                btn.textContent='Continue';
                btn.id='continue-name';
                btn.className='continue-btn';
                btn.onclick = ()=>{ showError(fullNameInput,''); btn.remove(); }
                fullNameInput.parentElement.appendChild(btn);
            }
        } else {
            showError(fullNameInput,'');
            let btn = document.querySelector('#continue-name');
            if(btn) btn.remove();
        }
    });
});

// --- Real-time Email check ---
emailInput.addEventListener('input',()=>{
    if(!emailRegex.test(emailInput.value)){
        showError(emailInput,'Invalid email format');
        return;
    }
    fetch('../controller/check_company_email.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'email='+encodeURIComponent(emailInput.value)
    }).then(r=>r.text()).then(resp=>{
        if(resp==='email_used'){
            showError(emailInput,'Email already used. Cannot register.');
        } else {
            showError(emailInput,'');
        }
    });
});

// --- Company check ---
companyInput.addEventListener('blur',()=>{
    fetch('../controller/check_company_email.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'company_name='+encodeURIComponent(companyInput.value)
    }).then(r=>r.text()).then(resp=>{
        if(resp==='company_blocked'){
            showError(companyInput,'This company already has 2 admins');
        } else {
            showError(companyInput,'');
        }
    });
});

// --- Password hints ---
passwordInput.addEventListener('input',()=>{
    const val = passwordInput.value;
    let hints = [];
    if(val.length<8) hints.push("8+ chars");
    if(!/[A-Z]/.test(val)) hints.push("uppercase");
    if(!/[a-z]/.test(val)) hints.push("lowercase");
    if(!/[0-9]/.test(val)) hints.push("number");
    if(!/[@$!%*?&]/.test(val)) hints.push("special char");
    showError(passwordInput, hints.join(', '));
});

// --- Form submission ---
document.querySelector('#registerFormElement').addEventListener('submit',function(e){
    e.preventDefault();
    let formData = new FormData(this);
    fetch('../controller/register_admin.php',{
        method:'POST',
        body: formData
    }).then(r=>r.json()).then(resp=>{
        for(let field of ['full_name','email','company_name','password']){
            if(resp.fields[field]) showError(document.querySelector(`[name="${field}"]`),resp.fields[field]);
            else showError(document.querySelector(`[name="${field}"]`),'');
        }
        if(resp.success){
            alert("Registered successfully!");
            location.reload();
        }
    });
});
// Name validation
fullNameInput.addEventListener('input',()=>{
    if(!nameRegex.test(fullNameInput.value)){
        showError(fullNameInput,'Invalid name');
        return;
    }
    fetch('../controller/check_name.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'full_name='+encodeURIComponent(fullNameInput.value)
    }).then(r=>r.text()).then(resp=>{
        if(resp==='exists'){
            showError(fullNameInput,'Name already exists.');
            if(!document.querySelector('#continue-name')){
                const btn = document.createElement('button');
                btn.type='button';
                btn.textContent='Continue';
                btn.id='continue-name';
                btn.className='continue-btn';
                btn.onclick = ()=>{ showError(fullNameInput,''); btn.remove(); }
                fullNameInput.parentElement.appendChild(btn);
            }
        } else {
            showError(fullNameInput,'');
            let btn = document.querySelector('#continue-name');
            if(btn) btn.remove();
        }
    });
});

// Company check
companyInput.addEventListener('blur', ()=>{
    fetch('../controller/check_company_email.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'company_name='+encodeURIComponent(companyInput.value)
    }).then(r=>r.text()).then(resp=>{
        if(resp==='company_blocked'){
            showError(companyInput,'A company is already registered. Cannot register new one.');
            companyInput.classList.add('input-error');
        } else {
            showError(companyInput,'');
            companyInput.classList.remove('input-error');
        }
    });
});

// Email check
emailInput.addEventListener('input', ()=>{
    if(!emailRegex.test(emailInput.value)){
        showError(emailInput,'Invalid email format');
        emailInput.classList.add('input-error');
        return;
    }
    fetch('../controller/check_company_email.php',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'email='+encodeURIComponent(emailInput.value)
    }).then(r=>r.text()).then(resp=>{
        if(resp==='email_used'){
            showError(emailInput,'Email already used. Cannot register.');
            emailInput.classList.add('input-error');
        } else {
            showError(emailInput,'');
            emailInput.classList.remove('input-error');
        }
    });
});

