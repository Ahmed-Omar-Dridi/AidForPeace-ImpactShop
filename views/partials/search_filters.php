<!-- Advanced Search & Filters -->
<div class="search-filters-container" id="searchFilters">
    <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" id="searchInput" placeholder="Rechercher un produit..." onkeyup="filterProducts()">
        <button class="filter-toggle" onclick="toggleFilters()">
            <i class="fas fa-sliders-h"></i> Filtres
        </button>
    </div>
    
    <div class="filters-panel" id="filtersPanel">
        <div class="filter-group">
            <h4><i class="fas fa-folder"></i> Catégorie</h4>
            <select id="filterCategory" onchange="filterProducts()">
                <option value="">Toutes les catégories</option>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name_fr']); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        
        <div class="filter-group">
            <h4><i class="fas fa-tag"></i> Prix</h4>
            <div class="price-range">
                <input type="number" id="priceMin" placeholder="Min" min="0" onchange="filterProducts()">
                <span>-</span>
                <input type="number" id="priceMax" placeholder="Max" min="0" onchange="filterProducts()">
                <span>TND</span>
            </div>
        </div>
        
        <div class="filter-group">
            <h4><i class="fas fa-box"></i> Disponibilité</h4>
            <label class="checkbox-label">
                <input type="checkbox" id="filterInStock" onchange="filterProducts()" checked>
                <span>En stock uniquement</span>
            </label>
        </div>
        
        <div class="filter-group">
            <h4><i class="fas fa-sort"></i> Trier par</h4>
            <select id="sortBy" onchange="sortProducts()">
                <option value="default">Par défaut</option>
                <option value="price_asc">Prix croissant</option>
                <option value="price_desc">Prix décroissant</option>
                <option value="name_asc">Nom A-Z</option>
                <option value="name_desc">Nom Z-A</option>
            </select>
        </div>
        
        <button class="reset-filters" onclick="resetFilters()">
            <i class="fas fa-undo"></i> Réinitialiser
        </button>
    </div>
    
    <div class="active-filters" id="activeFilters"></div>
    
    <div class="results-count" id="resultsCount"></div>
</div>

<style>
.search-filters-container {
    background: white;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.search-bar {
    display: flex;
    align-items: center;
    gap: 15px;
    background: #f8f9fa;
    padding: 10px 20px;
    border-radius: 50px;
    border: 2px solid transparent;
    transition: all 0.3s;
}

.search-bar:focus-within {
    border-color: #ffb600;
    background: white;
}

.search-bar i {
    color: #999;
    font-size: 1.1rem;
}

.search-bar input {
    flex: 1;
    border: none;
    background: none;
    font-size: 1rem;
    font-family: inherit;
    outline: none;
}

.filter-toggle {
    background: #1e3149;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.filter-toggle:hover {
    background: #ffb600;
    color: #1e3149;
}

.filters-panel {
    display: none;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.filters-panel.active {
    display: grid;
}

.filter-group h4 {
    color: #1e3149;
    font-size: 0.9rem;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-group h4 i {
    color: #ffb600;
}

.filter-group select {
    width: 100%;
    padding: 10px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-family: inherit;
    cursor: pointer;
}

.filter-group select:focus {
    outline: none;
    border-color: #ffb600;
}

.price-range {
    display: flex;
    align-items: center;
    gap: 10px;
}

.price-range input {
    width: 80px;
    padding: 10px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-family: inherit;
}

.price-range input:focus {
    outline: none;
    border-color: #ffb600;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    width: 20px;
    height: 20px;
    accent-color: #ffb600;
}

.reset-filters {
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
}

.reset-filters:hover {
    background: #e74c3c;
    border-color: #e74c3c;
    color: white;
}

.active-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
}

.filter-tag {
    background: #fff3cd;
    color: #856404;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-tag button {
    background: none;
    border: none;
    cursor: pointer;
    color: #856404;
    font-size: 1rem;
}

.results-count {
    margin-top: 15px;
    color: #666;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .search-bar {
        flex-wrap: wrap;
    }
    
    .search-bar input {
        width: 100%;
        order: 1;
    }
    
    .filter-toggle {
        order: 2;
    }
    
    .filters-panel {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
let allProducts = [];
let filteredProducts = [];

// Initialize products data
function initSearchFilters(products) {
    allProducts = products;
    filteredProducts = [...products];
    updateResultsCount();
}

function toggleFilters() {
    document.getElementById('filtersPanel').classList.toggle('active');
}

function filterProducts() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const category = document.getElementById('filterCategory').value;
    const priceMin = parseFloat(document.getElementById('priceMin').value) || 0;
    const priceMax = parseFloat(document.getElementById('priceMax').value) || Infinity;
    const inStockOnly = document.getElementById('filterInStock').checked;
    
    // Filter product cards in DOM
    const productCards = document.querySelectorAll('.product-card');
    let visibleCount = 0;
    
    productCards.forEach(card => {
        const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
        const description = card.querySelector('.description')?.textContent.toLowerCase() || '';
        const price = parseFloat(card.dataset.price) || 0;
        const stock = parseInt(card.dataset.stock) || 0;
        const catId = card.dataset.category || '';
        
        let visible = true;
        
        // Search filter
        if (searchTerm && !name.includes(searchTerm) && !description.includes(searchTerm)) {
            visible = false;
        }
        
        // Category filter
        if (category && catId !== category) {
            visible = false;
        }
        
        // Price filter
        if (price < priceMin || price > priceMax) {
            visible = false;
        }
        
        // Stock filter
        if (inStockOnly && stock <= 0) {
            visible = false;
        }
        
        card.style.display = visible ? '' : 'none';
        if (visible) visibleCount++;
    });
    
    updateActiveFilters();
    document.getElementById('resultsCount').textContent = `${visibleCount} produit(s) trouvé(s)`;
}

function sortProducts() {
    const sortBy = document.getElementById('sortBy').value;
    const container = document.querySelector('.products-grid');
    const cards = Array.from(container.querySelectorAll('.product-card'));
    
    cards.sort((a, b) => {
        const priceA = parseFloat(a.dataset.price) || 0;
        const priceB = parseFloat(b.dataset.price) || 0;
        const nameA = a.querySelector('h3')?.textContent || '';
        const nameB = b.querySelector('h3')?.textContent || '';
        
        switch(sortBy) {
            case 'price_asc': return priceA - priceB;
            case 'price_desc': return priceB - priceA;
            case 'name_asc': return nameA.localeCompare(nameB);
            case 'name_desc': return nameB.localeCompare(nameA);
            default: return 0;
        }
    });
    
    cards.forEach(card => container.appendChild(card));
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('filterCategory').value = '';
    document.getElementById('priceMin').value = '';
    document.getElementById('priceMax').value = '';
    document.getElementById('filterInStock').checked = true;
    document.getElementById('sortBy').value = 'default';
    
    filterProducts();
}

function updateActiveFilters() {
    const container = document.getElementById('activeFilters');
    container.innerHTML = '';
    
    const search = document.getElementById('searchInput').value;
    const category = document.getElementById('filterCategory');
    const priceMin = document.getElementById('priceMin').value;
    const priceMax = document.getElementById('priceMax').value;
    
    if (search) {
        addFilterTag(`Recherche: "${search}"`, () => { document.getElementById('searchInput').value = ''; filterProducts(); });
    }
    
    if (category.value) {
        addFilterTag(`Catégorie: ${category.options[category.selectedIndex].text}`, () => { category.value = ''; filterProducts(); });
    }
    
    if (priceMin || priceMax) {
        const priceText = priceMin && priceMax ? `${priceMin} - ${priceMax} TND` : priceMin ? `Min: ${priceMin} TND` : `Max: ${priceMax} TND`;
        addFilterTag(`Prix: ${priceText}`, () => { document.getElementById('priceMin').value = ''; document.getElementById('priceMax').value = ''; filterProducts(); });
    }
}

function addFilterTag(text, onRemove) {
    const container = document.getElementById('activeFilters');
    const tag = document.createElement('span');
    tag.className = 'filter-tag';
    tag.innerHTML = `${text} <button onclick="(${onRemove.toString()})()">×</button>`;
    container.appendChild(tag);
}

function updateResultsCount() {
    const visible = document.querySelectorAll('.product-card:not([style*="display: none"])').length;
    document.getElementById('resultsCount').textContent = `${visible} produit(s) trouvé(s)`;
}
</script>
