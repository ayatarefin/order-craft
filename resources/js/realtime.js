// Listen for file status changes in the order detail page
document.addEventListener('DOMContentLoaded', function() {
    if (window.orderId) {
        window.Echo.channel(`order.${window.orderId}`)
            .listen('.file.status.changed', (event) => {
                console.log('File status changed:', event);
                
                // Update file count stats
                updateFileStats();
                
                // Show notification
                showNotification(`File "${event.file_name}" ${event.new_status} by ${event.user_name}`);
                
                // If we're on the files page, update the UI
                if (document.getElementById('files-container')) {
                    updateFileUI(event);
                }
            });
    }
});

// Helper functions
function updateFileStats() {
    // Refresh the file stats via AJAX
    fetch(`/orders/${window.orderId}/stats`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('unclaimed-count').textContent = data.unclaimed;
            document.getElementById('in-progress-count').textContent = data.in_progress;
            document.getElementById('completed-count').textContent = data.completed;
            document.getElementById('total-count').textContent = data.total;
            
            // Update progress bars
            updateProgressBars(data);
        })
        .catch(error => console.error('Error fetching stats:', error));
}

function updateProgressBars(data) {
    const totalFiles = data.total;
    if (totalFiles > 0) {
        const unclaimedPercent = (data.unclaimed / totalFiles) * 100;
        const inProgressPercent = (data.in_progress / totalFiles) * 100;
        const completedPercent = (data.completed / totalFiles) * 100;
        
        const unclaimedBar = document.querySelector('.progress-bar.bg-secondary');
        if (unclaimedBar) {
            unclaimedBar.style.width = `${unclaimedPercent}%`;
            unclaimedBar.setAttribute('aria-valuenow', unclaimedPercent);
            unclaimedBar.textContent = `${Math.round(unclaimedPercent)}% Unclaimed`;
        }
        
        const inProgressBar = document.querySelector('.progress-bar.bg-warning');
        if (inProgressBar) {
            inProgressBar.style.width = `${inProgressPercent}%`;
            inProgressBar.setAttribute('aria-valuenow', inProgressPercent);
            inProgressBar.textContent = `${Math.round(inProgressPercent)}% In Progress`;
        }
        
        const completedBar = document.querySelector('.progress-bar.bg-success');
        if (completedBar) {
            completedBar.style.width = `${completedPercent}%`;
            completedBar.setAttribute('aria-valuenow', completedPercent);
            completedBar.textContent = `${Math.round(completedPercent)}% Completed`;
        }
    }
}

function updateFileUI(event) {
    const fileCard = document.querySelector(`.file-card[data-file-id="${event.file_id}"]`);
    if (fileCard) {
        // Update file card status badge
        const badge = fileCard.querySelector('.badge');
        if (badge) {
            badge.className = `badge bg-${event.new_status === 'completed' ? 'success' : (event.new_status === 'in_progress' ? 'warning' : 'secondary')}`;
            badge.textContent = event.new_status.charAt(0).toUpperCase() + event.new_status.slice(1);
        }
        
        // Update card border
        fileCard.className = fileCard.className.replace(/border-\w+/, '');
        fileCard.classList.add(`border-${event.new_status === 'completed' ? 'success' : (event.new_status === 'in_progress' ? 'warning' : '')}`);
        
        // Update claimed by info
        const userInfo = fileCard.querySelector('.card-text.small');
        if (event.new_status === 'in_progress') {
            if (!userInfo) {
                const cardBody = fileCard.querySelector('.card-body');
                if (cardBody) {
                    const userInfoElement = document.createElement('p');
                    userInfoElement.className = 'card-text small mb-0';
                    userInfoElement.innerHTML = `<i class="fas fa-user me-1 text-muted"></i> ${event.user_name}`;
                    cardBody.appendChild(userInfoElement);
                }
            } else {
                userInfo.innerHTML = `<i class="fas fa-user me-1 text-muted"></i> ${event.user_name}`;
            }
        }
        
        // Update action buttons
        const actionButtons = fileCard.querySelector('.card-footer .btn-group');
        if (actionButtons) {
            // Clear existing buttons
            actionButtons.innerHTML = '';
            
            // Add view button
            const viewButton = document.createElement('a');
            viewButton.href = `/files/${event.file_id}`;
            viewButton.className = 'btn btn-sm btn-outline-primary';
            viewButton.innerHTML = '<i class="fas fa-eye"></i>';
            actionButtons.appendChild(viewButton);
            
            // Add appropriate buttons based on status
            if (event.new_status === 'unclaimed') {
                const claimForm = document.createElement('form');
                claimForm.action = '/files/claim-batch';
                claimForm.method = 'POST';
                claimForm.className = 'd-inline';
                
                const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                claimForm.innerHTML = `
                    <input type="hidden" name="_token" value="${csrf}">
                    <input type="hidden" name="order_id" value="${event.order_id}">
                    <input type="hidden" name="file_id" value="${event.file_id}">
                    <input type="hidden" name="count" value="1">
                    <button type="submit" class="btn btn-sm btn-outline-primary" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                        <i class="fas fa-hand-paper"></i> Claim
                    </button>
                `;
                
                actionButtons.appendChild(claimForm);
            }
        }
    }
}

function showNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'toast show';
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    
    notification.innerHTML = `
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <small>Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}