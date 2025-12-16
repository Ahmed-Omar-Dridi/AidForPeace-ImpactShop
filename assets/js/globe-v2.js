console.log('🔖 GLOBE.JS VERSION 2.0 LOADED');
class InteractiveGlobe {
    constructor(containerId, countriesData) {
        console.log('🏗️ InteractiveGlobe constructor START');
        
        // SET THESE FIRST - BEFORE ANYTHING ELSE
        this.currentIndexMode = 'crisis';
        this.indexMarkers = {};
        
        console.log('✅ Core properties initialized:');
        console.log('   - currentIndexMode:', this.currentIndexMode);
        console.log('   - indexMarkers:', this.indexMarkers);
        
        // Now set other properties
        this.container = document.getElementById(containerId);
        this.countriesData = countriesData;
        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera(60, this.container.clientWidth / this.container.clientHeight, 0.1, 1000);
        this.renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
        this.markers = [];
        this.labels = [];

        console.log('🌍 Globe initialized with', countriesData.length, 'countries');
        
        this.init();
        this.animate();
        
        // FINAL VERIFICATION
        console.log('🔍 Final constructor verification:');
        console.log('   - this.currentIndexMode:', this.currentIndexMode);
        console.log('   - typeof this.currentIndexMode:', typeof this.currentIndexMode);
        console.log('   - this.indexMarkers keys:', Object.keys(this.indexMarkers).length);
        console.log('✅ InteractiveGlobe constructor END');
    }

    init() {
        this.scene.background = new THREE.Color(0x0a1a2f);
        this.renderer.setClearColor(0x0a1a2f);
        this.renderer.setPixelRatio(window.devicePixelRatio);
        this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
        this.container.appendChild(this.renderer.domElement);

        this.camera.position.z = 320;

        this.scene.add(new THREE.AmbientLight(0x223366, 0.6));
        const topLight = new THREE.DirectionalLight(0x4488ff, 1.3);
        topLight.position.set(0, 200, 100);
        this.scene.add(topLight);

        const fillLight = new THREE.DirectionalLight(0x3366cc, 0.8);
        fillLight.position.set(-100, -50, -100);
        this.scene.add(fillLight);

        this.controls = new THREE.OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.08;
        this.controls.minDistance = 180;
        this.controls.maxDistance = 550;

        this.createDarkBlueGlobe();
        this.addCountryBordersAndLabels();
        this.addCrisisMarkers();

        window.addEventListener('resize', () => this.onWindowResize());
        this.renderer.domElement.addEventListener('click', e => this.onClick(e));
        this.renderer.domElement.addEventListener('mousemove', e => this.onMouseMove(e));
    }

    createDarkBlueGlobe() {
        const geom = new THREE.SphereGeometry(100, 90, 90);
        const mat = new THREE.MeshPhongMaterial({
            color: 0x112244,
            emissive: 0x223366,
            emissiveIntensity: 0.7,
            shininess: 20,
            specular: 0x4488ff
        });
        this.globe = new THREE.Mesh(geom, mat);
        this.scene.add(this.globe);

        const halo = new THREE.Mesh(
            new THREE.SphereGeometry(107, 64, 64),
            new THREE.MeshBasicMaterial({
                color: 0x0088ff,
                transparent: true,
                opacity: 0.25,
                side: THREE.BackSide
            })
        );
        this.scene.add(halo);
    }

    addCountryBordersAndLabels() {
        fetch('https://cdn.jsdelivr.net/npm/world-atlas@2/countries-110m.json')
            .then(r => r.json())
            .then(topology => {
                const countries = topojson.feature(topology, topology.objects.countries);

                const borderMat = new THREE.LineBasicMaterial({ 
                    color: 0x66aaff, 
                    opacity: 0.9, 
                    transparent: true 
                });

                const fontLoader = new THREE.FontLoader();
                fontLoader.load('https://threejs.org/examples/fonts/helvetiker_regular.typeface.json', font => {
                    countries.features.forEach(feature => {
                        const name = feature.properties.name;
                        if (!name) return;

                        const coords = feature.geometry.coordinates;
                        const drawRing = ring => {
                            const points = ring.map(([lon, lat]) => this.latLonToVec3(lat, lon, 100.4));
                            const geom = new THREE.BufferGeometry().setFromPoints(points);
                            this.scene.add(new THREE.Line(geom, borderMat));
                        };
                        if (feature.geometry.type === "Polygon") coords.forEach(drawRing);
                        else coords.forEach(poly => poly.forEach(drawRing));

                        const [lon, lat] = this.getGoodCentroid(feature);
                        if (!lat || !lon) return;

                        const pos = this.latLonToVec3(lat, lon, 112);

                        const textGeo = new THREE.TextGeometry(name, {
                            font: font,
                            size: 2.4,
                            height: 0.1,
                            curveSegments: 6
                        });

                        const label = new THREE.Mesh(
                            textGeo,
                            new THREE.MeshBasicMaterial({ color: 0xaaccff })
                        );

                        label.position.copy(pos);
                        label.userData.billboard = true;

                        textGeo.computeBoundingBox();
                        const center = textGeo.boundingBox.getCenter(new THREE.Vector3());
                        label.position.sub(center.multiplyScalar(1.1));

                        this.scene.add(label);
                        this.labels.push(label);
                    });
                });
            });
    }

    getGoodCentroid(feature) {
        let totalX = 0, totalY = 0, count = 0;
        const traverse = coords => {
            coords.forEach(ring => {
                ring.forEach(([lon, lat]) => {
                    totalX += lon;
                    totalY += lat;
                    count++;
                });
            });
        };
        if (feature.geometry.type === "Polygon") traverse(feature.geometry.coordinates);
        else feature.geometry.coordinates.forEach(traverse);

        if (count === 0) return [0, 0];
        return [totalX / count, totalY / count];
    }

    latLonToVec3(lat, lon, radius = 100) {
        const phi = (90 - lat) * Math.PI / 180;
        const theta = (-lon) * Math.PI / 180;

        return new THREE.Vector3(
            radius * Math.sin(phi) * Math.cos(theta),
            radius * Math.cos(phi),
            radius * Math.sin(phi) * Math.sin(theta)
        );
    }

    addCrisisMarkers() {
        console.log('=== ADDING CRISIS MARKERS ===');
        
        this.countriesData.forEach(country => {
            if (!country.coords || country.coords.length !== 2) {
                console.warn('❌ Invalid coords for:', country.name, country.coords);
                return;
            }

            const [lon, lat] = country.coords;
            const pos = this.latLonToVec3(lat, lon, 106);

            // Default colors based on crisis level
            const colors = {
                Critical: 0xff3366,
                High: 0xff6600,
                Medium: 0xffaa00,
                Stable: 0x00ff88
            };
            const color = colors[country.crisis_level] || 0x888888;
            const size = country.crisis_level === 'Critical' ? 6.5 : 
                        country.crisis_level === 'High' ? 5.5 : 4.5;

            const marker = new THREE.Mesh(
                new THREE.SphereGeometry(size, 32, 32),
                new THREE.MeshBasicMaterial({ color })
            );
            marker.position.copy(pos);
            marker.userData = country;
            this.scene.add(marker);
            this.markers.push(marker);
            
            // Store marker by country name - CRITICAL for index updates
            this.indexMarkers[country.name] = marker;
            console.log(`✅ Marker created for: ${country.name}`);

            // Pulsing ring
            const ring = new THREE.Mesh(
                new THREE.RingGeometry(size * 1.8, size * 4.2, 40),
                new THREE.MeshBasicMaterial({ color, transparent: true, opacity: 0.5, side: THREE.DoubleSide })
            );
            ring.rotation.x = Math.PI / 2;
            marker.add(ring);

            const pulse = () => {
                new TWEEN.Tween(ring.scale).to({ x: 3.8, y: 3.8 }, 1600).easing(TWEEN.Easing.Cubic.Out).start();
                new TWEEN.Tween(ring.material).to({ opacity: 0 }, 1600)
                    .onComplete(() => { 
                        ring.scale.set(1, 1, 1); 
                        ring.material.opacity = 0.5; 
                    })
                    .start();
            };
            pulse();
            setInterval(pulse, 1800);
        });
        
        console.log('📊 Total markers created:', Object.keys(this.indexMarkers).length);
        console.log('📋 Available countries:', Object.keys(this.indexMarkers).join(', '));
        console.log('✅ currentIndexMode is set to:', this.currentIndexMode);
    }

    // =============================================================================
    // INDEX SYSTEM FUNCTIONS
    // =============================================================================
    
    // Switch to index mode
    switchToIndexMode(indexType) {
        console.log('🔄 Switching to index mode:', indexType);
        this.currentIndexMode = indexType;
        
        // Check if data functions exist
        if (typeof getCountryIndexValue === 'undefined') {
            console.error('❌ ERROR: countryIndexData.js not loaded!');
            alert('Error: Index data file not loaded. Please check that countryIndexData.js is included in your HTML before globe.js');
            return;
        }
        
        this.updateMarkerColors();
    }
    
    // Switch back to crisis level mode
    switchToCrisisMode() {
        console.log('🔄 Switching to crisis mode');
        this.currentIndexMode = 'crisis';
        this.updateMarkerColors();
    }
    
    // Update all marker colors based on current mode
    updateMarkerColors() {
        console.log('🎨 Updating marker colors for mode:', this.currentIndexMode);
        
        let updatedCount = 0;
        let notFoundCount = 0;
        
        this.countriesData.forEach(country => {
            const marker = this.indexMarkers[country.name];
            
            if (!marker) {
                console.warn(`❌ Marker not found for: ${country.name}`);
                notFoundCount++;
                return;
            }
            
            let newColor;
            
            if (this.currentIndexMode === 'crisis') {
                // Original crisis level colors
                const colors = {
                    Critical: 0xff3366,
                    High: 0xff6600,
                    Medium: 0xffaa00,
                    Stable: 0x00ff88
                };
                newColor = colors[country.crisis_level] || 0x888888;
                console.log(`   ${country.name}: Crisis=${country.crisis_level}, Color=0x${newColor.toString(16)}`);
            } else {
                // Index-based colors
                const indexValue = getCountryIndexValue(country.name, this.currentIndexMode);
                newColor = getColorForValue(indexValue);
                console.log(`   ${country.name}: ${this.currentIndexMode}=${indexValue}, Color=0x${newColor.toString(16)}`);
            }
            
            // Convert hex color to RGB (0-1 range) for Three.js
            const r = ((newColor >> 16) & 255) / 255;
            const g = ((newColor >> 8) & 255) / 255;
            const b = (newColor & 255) / 255;
            
            // Smoothly transition color
            new TWEEN.Tween(marker.material.color)
                .to({ r: r, g: g, b: b }, 800)
                .easing(TWEEN.Easing.Cubic.Out)
                .start();
            
            // Update ring color too
            if (marker.children[0]) {
                new TWEEN.Tween(marker.children[0].material.color)
                    .to({ r: r, g: g, b: b }, 800)
                    .easing(TWEEN.Easing.Cubic.Out)
                    .start();
            }
            
            updatedCount++;
        });
        
        console.log(`✅ Updated ${updatedCount} markers`);
        if (notFoundCount > 0) {
            console.warn(`⚠️ ${notFoundCount} markers not found`);
        }
    }

    // =============================================================================

    onClick(e) { 
        const mouse = this.getMouse(e); 
        const ray = new THREE.Raycaster(); 
        ray.setFromCamera(mouse, this.camera); 
        const hits = ray.intersectObjects(this.markers); 
        if (hits.length) { 
            this.flyToCountry(hits[0].object.userData); 
            this.showCountryInfo(hits[0].object.userData); 
        } 
    }
    
    onMouseMove(e) { 
        const mouse = this.getMouse(e); 
        const ray = new THREE.Raycaster(); 
        ray.setFromCamera(mouse, this.camera); 
        const hits = ray.intersectObjects(this.markers); 
        this.renderer.domElement.style.cursor = hits.length ? 'pointer' : 'grab'; 
        hits.length ? this.showTooltip(e, hits[0].object.userData) : this.hideTooltip(); 
    }
    
    getMouse(e) { 
        const r = this.renderer.domElement.getBoundingClientRect(); 
        return new THREE.Vector2(((e.clientX - r.left) / r.width) * 2 - 1, -((e.clientY - r.top) / r.height) * 2 + 1); 
    }

    showTooltip(e, c) {
        let t = document.getElementById('globe-tooltip');
        if (!t) { 
            t = document.createElement('div'); 
            t.id = 'globe-tooltip'; 
            Object.assign(t.style, {
                position: 'absolute',
                pointerEvents: 'none',
                background: 'rgba(13, 36, 66, 0.95)',
                color: '#e6f7ff',
                padding: '14px 20px',
                borderRadius: '12px',
                fontFamily: 'system-ui',
                fontSize: '16px',
                zIndex: 9999,
                backdropFilter: 'blur(12px)',
                border: '1px solid rgba(33, 150, 243, 0.3)',
                boxShadow: '0 10px 40px rgba(0,0,0,0.7)'
            }); 
            document.body.appendChild(t); 
        }
        
        // Show different info based on mode
        let content = `<strong style="font-size:19px">${c.name}</strong><br>`;
        
        if (this.currentIndexMode === 'crisis') {
            const col = c.crisis_level === 'Critical' ? '#ff3366' : 
                       c.crisis_level === 'High' ? '#ff6600' : 
                       c.crisis_level === 'Medium' ? '#ffdd00' : '#00ff99';
            content += `<span style="color:${col}">● ${c.crisis_level}</span><br>NGOs: ${c.ngos?.length ?? '—'}`;
        } else {
            // Check if data exists
            if (typeof getCountryIndexValue !== 'undefined' && typeof indexDefinitions !== 'undefined') {
                const indexValue = getCountryIndexValue(c.name, this.currentIndexMode);
                const indexDef = indexDefinitions[this.currentIndexMode];
                
                if (indexValue > 0) {
                    const severity = indexValue >= 80 ? 'Critical' : indexValue >= 60 ? 'Severe' : 
                                   indexValue >= 40 ? 'High' : indexValue >= 20 ? 'Medium' : 'Low';
                    const col = getColorForValue(indexValue);
                    const colStr = '#' + col.toString(16).padStart(6, '0');
                    content += `<span style="color:${colStr}">● ${indexDef.name}: ${indexValue}/100</span><br>`;
                    content += `<span style="font-size:13px; opacity:0.8">Severity: ${severity}</span>`;
                } else {
                    content += `<span style="opacity:0.7">No ${indexDef.name} data</span>`;
                }
            } else {
                content += `<span style="opacity:0.7">Index data not loaded</span>`;
            }
        }
        
        t.innerHTML = content;
        t.style.left = (e.clientX + 18) + 'px';
        t.style.top = (e.clientY + 18) + 'px';
        t.style.opacity = '1';
    }
    
    hideTooltip() { 
        const t = document.getElementById('globe-tooltip'); 
        if (t) t.style.opacity = '0'; 
    }

    flyToCountry(c) {
        const [lon, lat] = c.coords;
        const phi = (90 - lat) * Math.PI / 180;
        const theta = (-lon) * Math.PI / 180;

        const distance = 300;
        const targetPos = new THREE.Vector3(
            distance * Math.sin(phi) * Math.cos(theta),
            distance * Math.cos(phi),
            distance * Math.sin(phi) * Math.sin(theta)
        );

        const lookAtPos = new THREE.Vector3(
            100 * Math.sin(phi) * Math.cos(theta),
            100 * Math.cos(phi),
            100 * Math.sin(phi) * Math.sin(theta)
        );

        new TWEEN.Tween(this.camera.position)
            .to(targetPos, 2000)
            .easing(TWEEN.Easing.Cubic.InOut)
            .start();

        this.controls.target.copy(lookAtPos);
    }

    showCountryInfo(c) { 
        document.dispatchEvent(new CustomEvent('countrySelected', { detail: c })); 
    }

    onWindowResize() {
        this.camera.aspect = this.container.clientWidth / this.container.clientHeight;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(this.container.clientWidth, this.container.clientHeight);
    }

    animate() {
        requestAnimationFrame(() => this.animate());
        TWEEN.update();
        this.controls.update();

        this.labels.forEach(label => label.lookAt(this.camera.position));

        this.renderer.render(this.scene, this.camera);
    }
}