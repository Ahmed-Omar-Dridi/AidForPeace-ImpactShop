// =============================================================================
// COUNTRY INDEX DATA - Simple JavaScript Objects for School Project
// =============================================================================
// This file contains data for different social/economic indicators
// Values range from 0-100 (higher = worse situation)
// Easy to edit - just change the numbers!
// =============================================================================

// Main data storage - each country has values for different indexes
var countryIndexData = {
    // Format: 'CountryName': { hunger: value, poverty: value, crime: value, ... }
    
    'Ukraine': {
        hunger: 65,
        poverty: 70,
        crime: 45,
        migration: 85,
        waterAccess: 30,
        health: 60
    },
    
    'Syria': {
        hunger: 90,
        poverty: 85,
        crime: 75,
        migration: 95,
        waterAccess: 70,
        health: 80
    },
    
    'Yemen': {
        hunger: 95,
        poverty: 90,
        crime: 60,
        migration: 70,
        waterAccess: 85,
        health: 90
    },
    
    'Afghanistan': {
        hunger: 85,
        poverty: 88,
        crime: 70,
        migration: 80,
        waterAccess: 75,
        health: 85
    },
    
    'Somalia': {
        hunger: 92,
        poverty: 91,
        crime: 80,
        migration: 75,
        waterAccess: 90,
        health: 88
    },
    
    'South Sudan': {
        hunger: 88,
        poverty: 85,
        crime: 65,
        migration: 60,
        waterAccess: 80,
        health: 82
    },
    
    'Palestine': {
        hunger: 70,
        poverty: 75,
        crime: 50,
        migration: 65,
        waterAccess: 60,
        health: 65
    },
    
    'Haiti': {
        hunger: 78,
        poverty: 80,
        crime: 85,
        migration: 70,
        waterAccess: 65,
        health: 75
    },
    
    'Venezuela': {
        hunger: 72,
        poverty: 76,
        crime: 90,
        migration: 88,
        waterAccess: 45,
        health: 70
    },
    
    'Myanmar': {
        hunger: 68,
        poverty: 70,
        crime: 55,
        migration: 65,
        waterAccess: 50,
        health: 60
    },
    
    'Ethiopia': {
        hunger: 75,
        poverty: 78,
        crime: 50,
        migration: 55,
        waterAccess: 70,
        health: 68
    },
    
    'Chad': {
        hunger: 82,
        poverty: 84,
        crime: 60,
        migration: 50,
        waterAccess: 88,
        health: 80
    },
    
    'Nigeria': {
        hunger: 65,
        poverty: 68,
        crime: 75,
        migration: 60,
        waterAccess: 55,
        health: 62
    },
    
    'Tunisia': {
        hunger: 40,
        poverty: 35,
        crime: 30,
        migration: 45,
        waterAccess: 20,
        health: 35
    },
    
    'Lebanon': {
        hunger: 55,
        poverty: 60,
        crime: 45,
        migration: 70,
        waterAccess: 35,
        health: 50
    },
    

    
    'Japan': {
        hunger: 5,          // 🟩 GREEN - Excellent food security
        poverty: 15,        // 🟩 GREEN - Low poverty
        crime: 10,          // 🟩 GREEN - Very safe
        migration: 12,      // 🟩 GREEN - Stable population
        waterAccess: 8,     // 🟩 GREEN - Clean water everywhere
        health: 18          // 🟩 GREEN - Great healthcare
    },
    
    'Egypt': {
        hunger: 35,         // 🟨 YELLOW - Moderate food issues
        poverty: 42,        // 🟧 ORANGE - Significant poverty
        crime: 48,          // 🟧 ORANGE - Moderate crime
        migration: 25,      // 🟨 YELLOW - Some displacement
        waterAccess: 65,    // 🟥 RED - Water crisis!
        health: 38          // 🟨 YELLOW - Healthcare challenges
    },
    
    'France': {
        hunger: 8,          // 🟩 GREEN - Excellent
        poverty: 22,        // 🟨 YELLOW - Some inequality
        crime: 28,          // 🟨 YELLOW - Urban crime issues
        migration: 32,      // 🟨 YELLOW - Refugee pressure
        waterAccess: 5,     // 🟩 GREEN - Perfect water access
        health: 12          // 🟩 GREEN - Top healthcare
    },
    
    'colombia': {
        hunger: 25,         // 🟨 YELLOW - Improving
        poverty: 45,        // 🟧 ORANGE - Still significant
        crime: 72,          // 🟥 RED - High crime rate!
        migration: 55,      // 🟧 ORANGE - Internal displacement
        waterAccess: 28,    // 🟨 YELLOW - Rural issues
        health: 38          // 🟨 YELLOW - Healthcare gaps
    },
    
    'ALGERIA': {
        hunger: 18,         // 🟩 GREEN - Good food security
        poverty: 32,        // 🟨 YELLOW - Moderate
        crime: 24,          // 🟨 YELLOW - Relatively safe
        migration: 28,      // 🟨 YELLOW - Some movement
        waterAccess: 52,    // 🟧 ORANGE - Desert water scarcity
        health: 35          // 🟨 YELLOW - Decent healthcare
    },
    
    'england': {
        hunger: 12,         // 🟩 GREEN - Excellent
        poverty: 26,        // 🟨 YELLOW - Income inequality
        crime: 35,          // 🟨 YELLOW - Urban crime
        migration: 42,      // 🟧 ORANGE - Immigration debates
        waterAccess: 6,     // 🟩 GREEN - Perfect
        health: 16          // 🟩 GREEN - NHS healthcare
    },
    
    'iraq': {
        hunger: 68,         // 🟥 RED - Post-war food crisis
        poverty: 75,        // 🟥 RED - High poverty
        crime: 82,          // 🟥 RED - Violence and instability
        migration: 88,      // 🟥 RED - Mass displacement
        waterAccess: 71,    // 🟥 RED - Water infrastructure damaged
        health: 78          // 🟥 RED - Healthcare collapsed
    },
    
    'new york': {
        hunger: 14,         // 🟩 GREEN - Food available
        poverty: 38,        // 🟨 YELLOW - Income inequality
        crime: 44,          // 🟧 ORANGE - Urban crime issues
        migration: 22,      // 🟨 YELLOW - Immigration hub
        waterAccess: 10,    // 🟩 GREEN - Modern infrastructure
        health: 25          // 🟨 YELLOW - Healthcare costs
    },
    
    'london': {
        hunger: 11,         // 🟩 GREEN - Excellent
        poverty: 28,        // 🟨 YELLOW - Some inequality
        crime: 38,          // 🟨 YELLOW - Knife crime concerns
        migration: 36,      // 🟨 YELLOW - Diverse population
        waterAccess: 7,     // 🟩 GREEN - Perfect
        health: 14          // 🟩 GREEN - NHS access
    },
    
    'Sydney': {
        hunger: 9,          // 🟩 GREEN - Excellent
        poverty: 19,        // 🟩 GREEN - Low poverty
        crime: 21,          // 🟨 YELLOW - Generally safe
        migration: 24,      // 🟨 YELLOW - Immigration destination
        waterAccess: 32,    // 🟨 YELLOW - Drought concerns
        health: 15          // 🟩 GREEN - Good healthcare
    },
    
    'switzerland ': {      // Note: Keep the space to match database
        hunger: 6,          // 🟩 GREEN - Best food security
        poverty: 11,        // 🟩 GREEN - Very low poverty
        crime: 8,           // 🟩 GREEN - Safest country
        migration: 18,      // 🟩 GREEN - Controlled immigration
        waterAccess: 4,     // 🟩 GREEN - Alpine water abundance
        health: 7           // 🟩 GREEN - World-class healthcare
    }
};


var indexDefinitions = {
    'hunger': {
        name: 'Hunger Index',
        description: 'Food scarcity and malnutrition levels',
        unit: 'severity score'
    },
    'poverty': {
        name: 'Poverty Rate',
        description: 'Population living below poverty line',
        unit: 'percentage affected'
    },
    'crime': {
        name: 'Crime Rate',
        description: 'Violence and criminal activity levels',
        unit: 'safety risk score'
    },
    'migration': {
        name: 'Migration Pressure',
        description: 'People forced to leave their homes',
        unit: 'displacement score'
    },
    'waterAccess': {
        name: 'Water Access Crisis',
        description: 'Lack of clean drinking water',
        unit: 'scarcity score'
    },
    'health': {
        name: 'Health Crisis',
        description: 'Healthcare system and disease burden',
        unit: 'health risk score'
    }
};




function getCountryIndexValue(countryName, indexType) {
    // Check if country exists in our data
    if (countryIndexData[countryName]) {
        // Return the value for this index, or 0 if not found
        return countryIndexData[countryName][indexType] || 0;
    }
    // If country not found, return 0
    return 0;
}

// Get color based on value (0-100)
// Returns a color from green (good) to red (bad)
function getColorForValue(value) {
    if (value >= 80) return 0xff0000;  // Red - Critical (80-100)
    if (value >= 60) return 0xff4400;  // Orange-Red - Severe (60-79)
    if (value >= 40) return 0xff8800;  // Orange - High (40-59)
    if (value >= 20) return 0xffcc00;  // Yellow - Medium (20-39)
    return 0x00ff00;                    // Green - Low (0-19)
}

// Get all countries that have data
function getAllCountriesWithData() {
    var countries = [];
    for (var countryName in countryIndexData) {
        countries.push(countryName);
    }
    return countries;
}

// Check if a country has data for a specific index
function hasIndexData(countryName, indexType) {
    return countryIndexData[countryName] && 
           countryIndexData[countryName][indexType] !== undefined;
}


// =============================================================================
// COLOR LEGEND:
// =============================================================================
// 0-19:   Green       - Low/Good situation
// 20-39:  Yellow      - Medium concern
// 40-59:  Orange      - High concern
// 60-79:  Orange-Red  - Severe situation
// 80-100: Red         - Critical crisis
// =============================================================================