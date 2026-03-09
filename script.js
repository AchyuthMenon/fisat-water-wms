document.addEventListener('DOMContentLoaded', () => {
    // Nav Items mapped to logic
    const navItems = document.querySelectorAll('.sidebar-nav .nav-item[data-target]');
    const viewSections = document.querySelectorAll('.view-section');
    
    // Elements to update on switch
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
            
            // 1. Update active state on nav
            navItems.forEach(nav => nav.classList.remove('active'));
            item.classList.add('active');

            // 2. Hide all view sections
            viewSections.forEach(section => {
                section.classList.remove('active');
            });

            // 3. Show targeted view section
            const targetId = item.getAttribute('data-target');
            const targetEl = document.getElementById(targetId);
            if (targetEl) {
                targetEl.classList.add('active');
            }

            // 4. Update Header Titles dynamically
            if (viewData[targetId]) {
                pageTitle.textContent = viewData[targetId].title;
                pageSubtitle.textContent = viewData[targetId].subtitle;
            }
        });
    });
});

// Mock minimal modal functionality (frontend only)
function openModal(type) {
    if(type === 'building') {
        alert('Frontend Interaction: Open Add Building Dialog (Placeholder)');
    }
}
