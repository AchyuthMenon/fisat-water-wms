document.addEventListener('DOMContentLoaded', () => {
    const navItems = document.querySelectorAll('.sidebar-nav .nav-item[data-target]');
    const viewSections = document.querySelectorAll('.view-section');
    
    const pageTitle = document.getElementById('page-title');
    const pageSubtitle = document.getElementById('page-subtitle');

    const viewData = {
        'buildings-view': {
            title: 'Campus Buildings',
            subtitle: 'Manage college buildings where water is supplied.'
        },
        'tanks-view': {
            title: 'Water Storage Tanks',
            subtitle: 'Monitor storage capacity across all campus locations.'
        },
        'motors-view': {
            title: 'Motors & Pumps',
            subtitle: 'View specifications and connections for water transfer infrastructure.'
        },
        'usage-view': {
            title: 'Daily Water Usage',
            subtitle: 'Track consumption records and add new daily logs.'
        },
        'maintenance-view': {
            title: 'Maintenance Status',
            subtitle: 'Monitor working conditions and schedule repairs for motors.'
        }
    };

    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            
            navItems.forEach(nav => nav.classList.remove('active'));
            item.classList.add('active');

            viewSections.forEach(section => {
                section.classList.remove('active');
            });

            const targetId = item.getAttribute('data-target');
            const targetEl = document.getElementById(targetId);
            if (targetEl) {
                targetEl.classList.add('active');
            }

            if (viewData[targetId]) {
                pageTitle.textContent = viewData[targetId].title;
                pageSubtitle.textContent = viewData[targetId].subtitle;
            }
        });
    });
});

function openModal(type) {
    if(type === 'building') {
        alert('Frontend Interaction: Open Add Building Dialog (Placeholder)');
    }
}

function addRecord(tableName, id) {
    if(confirm("Are you sure you want to add a record?")) {
        fetch('add.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `table=${tableName}&id=${id}`
        })
        .then(response => response.text())
        .then(data => {
            if(data.trim() === "success") {
                location.reload();
            } else {
                alert("Error: " + data);
            }
        })
        .catch(error => {
            alert("Request failed");
        });
    }
}

function editUsage(usageId, currentAmount, date) {
    const newAmount = prompt(`Update water consumption for ${date} (in Liters):`, currentAmount);

    if (newAmount === null || newAmount.trim() === "") {
        return;
    }

    if (isNaN(newAmount) || Number(newAmount) < 0) {
        alert("Please enter a valid positive number for consumption.");
        return;
    }

    fetch('update_usage.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id=${encodeURIComponent(usageId)}&new_amount=${encodeURIComponent(newAmount)}`
    })
    .then(response => {
        if (!response.ok) throw new Error('Server returned ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            location.reload(); 
        } else {
            alert("✖ Error updating record: " + data.message);
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        alert("An error occurred while updating. Check the console.");
    });
}

function deleteRecord(tableName, id) {
    if(confirm("Are you sure you want to delete this record?")) {
        fetch('delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `table=${tableName}&id=${id}`
        })
        .then(response => response.text())
        .then(data => {
            if(data.trim() === "success") {
                location.reload();
            } else {
                alert("Error: " + data);
            }
        })
        .catch(error => {
            alert("Request failed");
        });
    }
}

document.getElementById('maintenanceForm').addEventListener('submit', function(e) {
    e.preventDefault(); 

    const feedback = document.getElementById('maintenanceFeedback');
    const formData = new FormData(this);

    const motorId = document.getElementById('motor_id').value;
    const issueDesc = document.getElementById('issue_description').value;
    const statusVal = document.getElementById('status').value;

    fetch('save_maintenance.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Server returned ' + response.status);
        return response.json(); 
    })
    .then(data => {
        if(data.status === 'success') {
            feedback.style.color = "#2ecc71";
            feedback.innerHTML = "✔ Record saved successfully!";
            
            const tbody = document.querySelector('#maintenance-view tbody');
            
            if (tbody.querySelector('td[colspan]')) {
                tbody.innerHTML = '';
            }

            const newRow = document.createElement('tr');
            
            newRow.innerHTML = `
                <td>${motorId}</td>
                <td><em>Updating...</em></td> <td><strong>${statusVal}</strong></td>
                <td>${issueDesc}</td>
                <td>
                    <div class="card-actions">
                        <button onclick="deleteRecord('maintenance', ${data.id})" class="btn-icon text-danger" title="Delete Log">
                            <i class="ph ph-trash"></i>
                        </button>
                    </div>
                </td>
            `;

            tbody.insertBefore(newRow, tbody.firstChild);

            this.reset();
            
            setTimeout(() => { feedback.innerHTML = ''; }, 3000);
        } else {
            feedback.style.color = "#e74c3c";
            feedback.innerHTML = "✖ Error: " + data.message;
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        feedback.style.color = "#e74c3c";
        feedback.innerHTML = "An error occurred. Check Console (F12).";
    });
});

document.getElementById('usageForm').addEventListener('submit', function(e) {
    e.preventDefault(); 

    const feedback = document.getElementById('formFeedback');
    const formData = new FormData(this);

    const dateVal = document.getElementById('usage_date').value;
    const buildingSelect = document.getElementById('building_id');
    const consumptionVal = document.getElementById('consumption').value;
    
    const buildingText = buildingSelect.options[buildingSelect.selectedIndex].text;

    fetch('save_usage.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Server returned ' + response.status);
        return response.json(); 
    })
    .then(data => {
        if(data.status === 'success') {
            feedback.style.color = "#2ecc71";
            feedback.innerHTML = "✔ Record saved successfully!";
            
            const tbody = document.querySelector('#usage-view table tbody');
            
            if (tbody.querySelector('td[colspan]')) {
                tbody.innerHTML = '';
            }

            const newRow = document.createElement('tr');
            
            const formattedConsumption = Number(consumptionVal).toLocaleString();

            newRow.innerHTML = `
                <td>${dateVal}</td>
                <td>${buildingText}</td>
                <td><strong>${formattedConsumption} L</strong></td>
                <td>
                    <button onclick='editUsage(${data.id}, ${consumptionVal}, "${dateVal}")' 
                        class='btn-icon text-primary' title='Edit'>
                        <i class='ph ph-pencil-simple'></i>
                    </button>
                    <button onclick='deleteRecord("water_usage", ${data.id})' 
                        class='btn-icon text-danger' title='Delete'>
                        <i class='ph ph-trash'></i>
                    </button>
                </td>
            `;

            tbody.insertBefore(newRow, tbody.firstChild);

            this.reset();
            
            setTimeout(() => { feedback.innerHTML = ''; }, 3000);
        } else {
            feedback.style.color = "#e74c3c";
            feedback.innerHTML = "✖ Error: " + data.message;
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        feedback.style.color = "#e74c3c";
        feedback.innerHTML = "An error occurred. Check Console (F12).";
    });
});

const motorSearchInput = document.getElementById('motorSearch');
if (motorSearchInput) {
    motorSearchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const motorCards = document.querySelectorAll('#motorCardGrid .motor-card');

        motorCards.forEach(card => {
            const cardText = card.textContent.toLowerCase();
            
            if (cardText.includes(searchTerm)) {
                card.style.display = ''; 
            } else {
                card.style.display = 'none'; 
            }
        });
    });
}

const addMotorForm = document.getElementById('addMotorForm');
if (addMotorForm) {
    addMotorForm.addEventListener('submit', function(e) {
        e.preventDefault(); 
        
        const feedback = document.getElementById('motorFeedback');
        const formData = new FormData(this);

        fetch('save_motor.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Server returned ' + response.status);
            return response.json(); 
        })
        .then(data => {
            if(data.status === 'success') {
                feedback.style.color = "#2ecc71";
                feedback.innerHTML = "✔ Motor added successfully!";
                this.reset();
                
                setTimeout(() => location.reload(), 1000);
            } else {
                feedback.style.color = "#e74c3c";
                feedback.innerHTML = "✖ Error: " + data.message;
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            feedback.style.color = "#e74c3c";
            feedback.innerHTML = "An error occurred. Check Console (F12).";
        });
    });
}

const tankSearchInput = document.getElementById('tankSearch');
if (tankSearchInput) {
    tankSearchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const tankRows = document.querySelectorAll('#tanksTable tbody tr');

        tankRows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            
            if (rowText.includes(searchTerm)) {
                row.style.display = ''; 
            } else {
                row.style.display = 'none'; 
            }
        });
    });
}

const addTankForm = document.getElementById('addTankForm');
if (addTankForm) {
    addTankForm.addEventListener('submit', function(e) {
        e.preventDefault(); 
        
        const feedback = document.getElementById('tankFeedback');
        const formData = new FormData(this);

        fetch('save_tank.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Server returned ' + response.status);
            return response.json(); 
        })
        .then(data => {
            if(data.status === 'success') {
                feedback.style.color = "#2ecc71";
                feedback.innerHTML = "✔ Tank added successfully!";
                this.reset();
                setTimeout(() => location.reload(), 1000);
            } else {
                feedback.style.color = "#e74c3c";
                feedback.innerHTML = "✖ Error: " + data.message;
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            feedback.style.color = "#e74c3c";
            feedback.innerHTML = "An error occurred. Check Console (F12).";
        });
    });
}