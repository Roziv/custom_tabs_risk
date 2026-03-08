document.addEventListener('DOMContentLoaded', function() {
    const tabGroups = document.querySelectorAll('.custom-tabs-wrapper');
    
    tabGroups.forEach(group => {
        const buttons = group.querySelectorAll('.custom-tab-button');
        const panels = group.querySelectorAll('.custom-tab-panel');
        
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons and panels in this group
                buttons.forEach(btn => btn.classList.remove('active'));
                panels.forEach(panel => panel.classList.remove('active'));
                
                // Add active class to clicked button
                button.classList.add('active');
                
                // Show the corresponding panel
                const targetId = button.getAttribute('data-tab');
                const targetPanel = group.querySelector('#' + targetId);
                if (targetPanel) {
                    targetPanel.classList.add('active');
                }
            });
        });
    });
});
