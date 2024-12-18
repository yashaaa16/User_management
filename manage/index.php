<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
    <style>
        /* Reset default margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body and General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #2c3e50;
            margin: 20px;
        }

        h1 {
            text-align: center;
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #3498db;
        }

        form, table, .stats {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Form Styles */
        form label {
            font-size: 1.1em;
            margin-top: 15px;
            display: block;
            color: #2c3e50;
            font-weight: bold;
        }

        form input, form select, button {
            margin-top: 8px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #bdc3c7;
            width: 100%;
            font-size: 1em;
        }

        form input[type="text"], form input[type="email"], form select {
            background-color: #ecf0f1;
            color: #2c3e50;
        }

        button {
            background-color: #3498db;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 1.1em;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px 20px;
            text-align: center;
            font-size: 1.1em;
            border: 1px solid #bdc3c7;
        }

        th {
            background-color: #3498db;
            color: #fff;
        }

        td {
            background-color: #ecf0f1;
        }

        tr:hover {
            background-color: #d6eaf8;
            cursor: pointer;
        }

        td .actions button {
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        td .actions button.edit {
            background-color: #f39c12;
        }

        td .actions button:hover {
            background-color: #c0392b;
        }

        td .actions button.edit:hover {
            background-color: #d35400;
        }

        /* Stats Section */
        .stats p {
            font-size: 1.2em;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Search and Filter Styles */
        #search, #roleFilter, #statusFilter {
            padding: 10px;
            font-size: 1.1em;
            border-radius: 8px;
            margin-top: 20px;
            width: calc(30% - 10px);
            border: 1px solid #bdc3c7;
            background-color: #ecf0f1;
            color: #2c3e50;
        }

        #search {
            margin-right: 5%;
        }

        #roleFilter {
            margin-right: 5%;
        }

        .highlight {
            background-color: rgba(52, 152, 219, 0.3);
        }

        @media (max-width: 768px) {
            form input, form select, button {
                width: 100%;
            }

            #search, #roleFilter, #statusFilter {
                width: 100%;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <h1>User Management System</h1>
    <form id="userForm">
        <h3>Add New User</h3>
        <label>Name:</label>
        <input type="text" id="name" required>
        <label>Email:</label>
        <input type="email" id="email" required>
        <label>Phone Number:</label>
        <input type="text" id="phone" required>
        <label>Role:</label>
        <select id="role">
            <option>Admin</option>
            <option>Viewer</option>
            <option>Editor</option>
        </select>
        <label>Status:</label>
        <select id="status">
            <option>Active</option>
            <option>Inactive</option>
        </select>
        <button type="submit">Add User</button>
    </form>

    <div>
        <input type="text" id="search" placeholder="Search by name...">
        <select id="roleFilter">
            <option value="">All Roles</option>
            <option>Admin</option>
            <option>Viewer</option>
            <option>Editor</option>
        </select>
        <select id="statusFilter">
            <option value="">All Statuses</option>
            <option>Active</option>
            <option>Inactive</option>
        </select>
    </div>

    <div class="stats">
        <p>Total Users: <span id="totalUsers">0</span> | Active: <span id="activeUsers">0</span> | Inactive: <span id="inactiveUsers">0</span></p>
    </div>

    <table>
        <thead>
            <tr>
                <th onclick="sortTable(0)">Name</th>
                <th onclick="sortTable(1)">Email</th>
                <th onclick="sortTable(2)">Phone</th>
                <th onclick="sortTable(3)">Role</th>
                <th onclick="sortTable(4)">Status</th>
                <th onclick="sortTable(5)">Registration Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="userTableBody"></tbody>
    </table>

    <script>
        const userForm = document.getElementById("userForm");
        const userTableBody = document.getElementById("userTableBody");
        const searchInput = document.getElementById("search");
        const roleFilter = document.getElementById("roleFilter");
        const statusFilter = document.getElementById("statusFilter");

        let users = [];
        let lastAddedIndex = -1;

        function loadUsersFromLocalStorage() {
            const storedUsers = localStorage.getItem("users");
            if (storedUsers) {
                users = JSON.parse(storedUsers);
            }
        }

        function saveUsersToLocalStorage() {
            localStorage.setItem("users", JSON.stringify(users));
        }

        function updateUserStats() {
            const totalUsers = users.length;
            const activeUsers = users.filter(user => user.status === "Active").length;
            const inactiveUsers = totalUsers - activeUsers;

            document.getElementById("totalUsers").innerText = totalUsers;
            document.getElementById("activeUsers").innerText = activeUsers;
            document.getElementById("inactiveUsers").innerText = inactiveUsers;
        }

        function renderTable() {
            userTableBody.innerHTML = "";
            const searchTerm = searchInput.value.trim().toLowerCase();
            const roleTerm = roleFilter.value;
            const statusTerm = statusFilter.value;

            users.forEach((user, index) => {
                const nameMatch = user.name.toLowerCase().includes(searchTerm);
                const roleMatch = roleTerm === "" || user.role === roleTerm;
                const statusMatch = statusTerm === "" || user.status === statusTerm;

                if (nameMatch && roleMatch && statusMatch) {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.phone}</td>
                        <td>${user.role}</td>
                        <td>${user.status}</td>
                        <td>${new Date(user.registrationDate).toLocaleString()}</td>
                        <td class="actions">
                            <button class="edit" onclick="editUser(${index})">Edit</button>
                            <button onclick="toggleStatus(${index})">${user.status === "Active" ? "Change to Inactive" : "Change to Active"}</button>
                            <button onclick="deleteUser(${index})">Delete</button>
                        </td>
                    `;

                    if (new Date() - new Date(user.registrationDate) < 10 * 60 * 1000) {
                        row.classList.add("highlight");
                    }

                    userTableBody.appendChild(row);
                }
            });

            updateUserStats();
            saveUsersToLocalStorage();
        }

        userForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const phoneInput = document.getElementById("phone");
            const phoneValue = phoneInput.value.trim();

            if (isNaN(phoneValue) || phoneValue === "") {
                alert("Phone number must be numeric and not empty!");
                phoneInput.focus();
                return;
            }

            const newUser = {
                name: document.getElementById("name").value.trim(),
                email: document.getElementById("email").value.trim(),
                phone: phoneValue,
                role: document.getElementById("role").value,
                status: document.getElementById("status").value,
                registrationDate: new Date().toISOString()
            };

            users.unshift(newUser);
            lastAddedIndex = 0;
            userForm.reset();
            renderTable();
        });

        function deleteUser(index) {
            users.splice(index, 1);
            lastAddedIndex = -1;
            renderTable();
        }

        function toggleStatus(index) {
            const user = users[index];
            user.status = user.status === "Active" ? "Inactive" : "Active";
            renderTable();
        }
// editing User
        function editUser(index) {
            const user = users[index];
            const name = prompt("Enter new name:", user.name);
            const email = prompt("Enter new email:", user.email);
            const phone = prompt("Enter new phone (numbers only):", user.phone);
            if (isNaN(phone) || phone.trim() === "") {
                alert("Phone number must be numeric and not empty!");
                return;
            }
            const role = prompt("Enter new role (Admin/Viewer/Editor):", user.role);
            const status = prompt("Enter new status (Active/Inactive):", user.status);

            if (name && email && phone && role && status) {
                users[index] = { name, email, phone, role, status, registrationDate: user.registrationDate };
                renderTable();
            }
        }

        function sortTable(columnIndex) {
            users.sort((a, b) => {
                const aValue = columnIndex === 5 
                    ? new Date(a.registrationDate).getTime() 
                    : Object.values(a)[columnIndex].toLowerCase();
                const bValue = columnIndex === 5 
                    ? new Date(b.registrationDate).getTime() 
                    : Object.values(b)[columnIndex].toLowerCase();

                return aValue > bValue ? 1 : -1;
            });

            renderTable();
        }

        searchInput.addEventListener("input", renderTable);
        roleFilter.addEventListener("change", renderTable);
        statusFilter.addEventListener("change", renderTable);

        loadUsersFromLocalStorage();
        renderTable();
    </script>
</body>
</html>
