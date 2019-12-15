// Get the menu
var configMenu = document.getElementById("configMenu");

// Get mobile menu
var mobileConfigMenu = document.getElementById("mobileConfigMenu");

// Get all buttons inside the container
var menuBtns = configMenu.getElementsByClassName("configmenubutton");

// Get all buttons inside mobile container
var mobileMenuBtns = mobileConfigMenu.getElementsByClassName("configmenubutton");

// Array of all the section IDs
var configSections = [
    document.getElementById("grundsmage"),
    document.getElementById("ekstraSmage"),
    document.getElementById("striber"),
    document.getElementById("knas"),
    document.getElementById("kurv")
];

var configText = document.getElementById("headConfigText");

var mobileToggle = document.getElementById("mobileToggle");

var nextChev = document.getElementById("nextChev");
var prevChev = document.getElementById("prevChev");

// Loop through the buttons and add the active class to the current/clicked button
for (let i = 0; i < menuBtns.length; i++) {
    menuBtns[i].addEventListener("click", function () {
        let currentMenu = document.getElementsByClassName(" active");
        currentMenu[0].className = currentMenu[0].className.replace(" active", "");
        this.className += " active";
    });
};

for (let i = 0; i < mobileMenuBtns.length; i++) {
    mobileMenuBtns[i].addEventListener("click", function () {
        let currentMenu = document.getElementsByClassName(" active");
        currentMenu[0].className = currentMenu[0].className.replace(" active", "");
        this.className += " active";
    });
};

ChangeSection = (id) => {
    let current = document.getElementsByClassName(" active-section");
    current[0].className = current[0].className.replace(" active-section", "");
    id.className += " active-section";
    if (id == grundsmage) {
        configText.innerHTML = "Vælg en grundsmag til MinParadis";
        mobileToggle.innerHTML = "Grundsmag";
        prevChev.style.visibility = "hidden";
        nextChev.style.visibility = "visible";
    }
    if (id == ekstraSmage) {
        configText.innerHTML = "Vælg ekstra smage til MinParadis (valgfrit)";
        mobileToggle.innerHTML = "Ekstra smage <i class=\"counter\">0/2</i>";
        prevChev.style.visibility = "visible";
        nextChev.style.visibility = "visible";
    }
    if (id == striber) {
        configText.innerHTML = "Vælg striber til MinParadis (valgfrit)";
        mobileToggle.innerHTML = "Striber <i class=\"counter\">0/2</i>";
        prevChev.style.visibility = "visible";
        nextChev.style.visibility = "visible";
    }
    if (id == knas) {
        configText.innerHTML = "Vælg knasende stykker til MinParadis (valgfrit)";
        mobileToggle.innerHTML = "Knas og stykker <i class=\"counter\">0/2</i>";
        prevChev.style.visibility = "visible";
        nextChev.style.visibility = "visible";
    }
    if (id == kurv) {
        configText.innerHTML = "Bekræft dine valg og føj MinParadis til din kurv";
        mobileToggle.innerHTML = "Føj til kurv";
        prevChev.style.visibility = "visible";
        nextChev.style.visibility = "hidden";
    }
    // id == grundsmage ? configText.innerHTML = "Vælg en grundsmag til MinParadis." : null;
    // id == ekstraSmage ? configText.innerHTML = "Vælg ekstra smage til MinParadis. (Valgfrit)" : null;
    // id == striber ? configText.innerHTML = "Vælg striber til MinParadis. (Valgfrit)" : null;
    // id == knas ? configText.innerHTML = "Vælg knasende stykker til MinParadis. (Valgfrit)" : null;
    // id == kurv ? configText.innerHTML = "Bekræft dine valg og føj MinParadis til din kurv." : null;
};

// ChangeSectionMobile = (id) => {
//     let current = document.getElementsByClassName(" active-section");
//     current[0].className = current[0].className.replace(" active-section", "");
//     id.className += " active-section";
//     id == grundsmage ? configText.innerHTML = "Vælg en grundsmag til MinParadis." : null;
//     id == ekstraSmage ? configText.innerHTML = "Vælg ekstra smage til MinParadis. (Valgfrit)" : null;
//     id == striber ? configText.innerHTML = "Vælg striber til MinParadis. (Valgfrit)" : null;
//     id == knas ? configText.innerHTML = "Vælg knasende stykker til MinParadis. (Valgfrit)" : null;
//     id == kurv ? configText.innerHTML = "Bekræft dine valg og føj MinParadis til din kurv." : null;
// };

NextSection = () => {
    let current = document.getElementsByClassName(" active-section");
    let currentMenu = document.getElementsByClassName(" active");
    var currentID = current[0].id;
    for (var i = 0; i < configSections.length; i++) {
        // Checks if current section is the last section and prevents the user from going further forwards
        if (currentID == configSections[configSections.length - 1].id) {
            return;
        }
        if (currentID == configSections[i].id) {
            configSections[i].className = configSections[i].className.replace(" active-section", "");
            currentMenu[0].className = currentMenu[0].className.replace(" active", "");
            i++;
            configSections[i].className += " active-section";
            menuBtns[i].className += " active";
        }
        if (currentID == configSections[0].id) {
            configText.innerHTML = "Vælg ekstra smage til MinParadis (valgfrit)";
            mobileToggle.innerHTML = "Ekstra smage <i class=\"counter\">0/2</i>";
            prevChev.style.visibility = "visible";
        }
        if (currentID == configSections[1].id) {
            configText.innerHTML = "Vælg striber til MinParadis (valgfrit)";
            mobileToggle.innerHTML = "Striber <i class=\"counter\">0/2</i>";
        }
        if (currentID == configSections[2].id) {
            configText.innerHTML = "Vælg knasende stykker til MinParadis (valgfrit)";
            mobileToggle.innerHTML = "Knas og stykker <i class=\"counter\">0/2</i>";
        }
        if (currentID == configSections[3].id) {
            configText.innerHTML = "Bekræft dine valg og føj MinParadis til din kurv";
            mobileToggle.innerHTML = "Føj til kurv";
            nextChev.style.visibility = "hidden";
        }
        // currentID == configSections[0].id ? configText.innerHTML = "Vælg ekstra smage til MinParadis. (Valgfrit)" : null;
        // currentID == configSections[1].id ? configText.innerHTML = "Vælg striber til MinParadis. (Valgfrit)" : null;
        // currentID == configSections[2].id ? configText.innerHTML = "Vælg knasende stykker til MinParadis. (Valgfrit)" : null;
        // currentID == configSections[3].id ? configText.innerHTML = "Bekræft dine valg og føj MinParadis til din kurv." : null;
    }
};

PrevSection = () => {
    let current = document.getElementsByClassName(" active-section");
    let currentMenu = document.getElementsByClassName(" active");
    var currentID = current[0].id;
    for (var i = 0; i < configSections.length; i++) {
        // Checks if current section is the first section and prevents the user from going further back
        if (currentID == configSections[0].id) {
            return;
        }
        if (currentID == configSections[i].id) {
            configSections[i].className = configSections[i].className.replace(" active-section", "");
            currentMenu[0].className = currentMenu[0].className.replace(" active", "");
            let e = i;
            e--;
            configSections[e].className += " active-section";
            menuBtns[e].className += " active";
        }
        if (currentID == configSections[1].id) {
            configText.innerHTML = "Vælg en grundsmag til MinParadis.";
            mobileToggle.innerHTML = "Grundsmag";
            prevChev.style.visibility = "hidden";
        }
        if (currentID == configSections[2].id) {
            configText.innerHTML = "Vælg ekstra smage til MinParadis (valgfrit)";
            mobileToggle.innerHTML = "Ekstra smage <i class=\"counter\">0/2</i>";
        }
        if (currentID == configSections[3].id) {
            configText.innerHTML = "Vælg striber til MinParadis (valgfrit)";
            mobileToggle.innerHTML = "Striber <i class=\"counter\">0/2</i>";
        }
        if (currentID == configSections[4].id) {
            configText.innerHTML = "Vælg knasende stykker til MinParadis (valgfrit)";
            mobileToggle.innerHTML = "Knas og stykker <i class=\"counter\">0/2</i>";
            nextChev.style.visibility = "visible";
        }
        // currentID == configSections[1].id ? configText.innerHTML = "Vælg en grundsmag til MinParadis." : null;
        // currentID == configSections[2].id ? configText.innerHTML = "Vælg ekstra smage til MinParadis. (Valgfrit)" : null;
        // currentID == configSections[3].id ? configText.innerHTML = "Vælg striber til MinParadis. (Valgfrit)" : null;
        // currentID == configSections[4].id ? configText.innerHTML = "Vælg knasende stykker til MinParadis. (Valgfrit)" : null;
    }
}

ToggleMenu = () => {
    for (let i = 0; i < mobileMenuBtns.length; i++) {
        mobileMenuBtns[i].style.display = mobileMenuBtns[i].style.display === 'inline-block' ? 'none' : 'inline-block';
        // if (mobileMenuBtns[i].style.display = mobileMenuBtns[i].style.display === 'inline-block' && mobileMenuBtns[i].className != "configmenubutton active") {
        //     mobileMenuBtns[i].style.display = "none";
        // } else {
        //     mobileMenuBtns[i].style.display = "inline-block";
        // }
        // if (mobileMenuBtns[i].style.display == "none") {
        //     console.log(menuBtns[i]);
        //     mobileMenuBtns[i].style.display = "inline-block";
        // } else if (mobileMenuBtns[i].style.display == "inline-block" && mobileMenuBtns[i].className != " active") {
        //     mobileMenuBtns[i].style.display = "none";
        // }
    }
}