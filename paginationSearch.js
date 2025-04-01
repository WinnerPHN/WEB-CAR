document.addEventListener("DOMContentLoaded", function () {
    const itemsPerPage = 6; // Number of items to display per page
    const allItems = document.querySelectorAll('.box'); // All items
    const filterItems = document.querySelectorAll('.tab-item'); // All filter tabs
    const paginationContainer = document.getElementById('pagination-controls');
    const searchInput = document.getElementById('search-item');

    let currentPage = 1;
    let currentFilter = 'all';

    // Function to show items based on pagination and filter
    function showItems(page = 1, filter = 'all') {
        const filteredItems = filterItemsByCategory(filter);
        const startIndex = (page - 1) * itemsPerPage;
        const endIndex = page * itemsPerPage;
        
        // Hide all items first
        allItems.forEach(item => item.style.display = 'none');
        
        // Show the filtered and paginated items
        filteredItems.slice(startIndex, endIndex).forEach(item => item.style.display = 'block');
        
        // Update pagination controls
        updatePagination(filteredItems.length, page);
    }

    // Function to filter items based on category
    function filterItemsByCategory(category) {
        return Array.from(allItems).filter(item => {
            if (category === 'all') {
                return true;
            }
            return item.dataset.item === category;
        });
    }

    // Function to update pagination controls
    function updatePagination(totalItems, page) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        paginationContainer.innerHTML = '';

        // Create page buttons
        for (let i = 1; i <= totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i;
            pageButton.classList.add('page-button');
            if (i === page) {
                pageButton.classList.add('active');
            }

            pageButton.addEventListener('click', () => {
                currentPage = i;
                showItems(currentPage, currentFilter);
            });

            paginationContainer.appendChild(pageButton);
        }
    }

    // Event listener for filter category change
    filterItems.forEach(tab => {
        tab.addEventListener('click', function () {
            currentFilter = this.getAttribute('data-filter');
            currentPage = 1; // Reset to page 1 when a new category is selected
            showItems(currentPage, currentFilter);

            // Toggle active class for tabs
            filterItems.forEach(item => item.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Event listener for search input
    searchInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {  // Check if the Enter key is pressed
            const searchQuery = this.value.toLowerCase();
            if (searchQuery === '') {
                // If the search query is empty, reset the display to match the current filter and pagination
                showItems(currentPage, currentFilter);
            } else {
                // If the search query is not empty, filter the items based on the search
                const searchFilteredItems = Array.from(allItems).filter(item => {
                    const name = item.querySelector('.name_item').textContent.toLowerCase();
                    return name.includes(searchQuery);
                });

                // Hide all items and display only the ones that match the search query
                allItems.forEach(item => item.style.display = 'none');
                searchFilteredItems.forEach(item => item.style.display = 'block');
                
                // Update pagination based on the filtered search results
                updatePagination(searchFilteredItems.length, currentPage);
            }
        }
    });

    // Initialize
    showItems(currentPage, currentFilter);
});
