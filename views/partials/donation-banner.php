<!-- Donation Banner -->
<div style="background: linear-gradient(135deg, #1e3149 0%, #15202e 100%); border-radius: 15px; padding: 30px; margin: 30px 0; position: relative; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
    <div style="position: absolute; top: -50%; right: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,182,0,0.1) 0%, transparent 70%); animation: rotate-3d 20s linear infinite; pointer-events: none;"></div>
    
    <div style="position: relative; z-index: 1; display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: center;">
        <div>
            <h3 style="color: white; font-size: 1.8rem; margin-bottom: 15px; font-weight: 800;">
                <i class="fas fa-heart" style="color: #ffb600; margin-right: 10px;"></i>Faire la Différence
            </h3>
            <p style="color: rgba(255,255,255,0.9); font-size: 1rem; line-height: 1.6; margin-bottom: 15px;">
                Votre générosité change des vies. Chaque don, peu importe le montant, aide nos bénéficiaires à construire un meilleur avenir.
            </p>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="index.php?controller=home&action=donation" style="background: linear-gradient(135deg, #ffb600 0%, #ffc933 100%); color: #1e3149; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-weight: 700; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 8px 25px rgba(255,182,0,0.4);" onmouseover="this.style.transform='translateY(-3px) scale(1.05)'; this.style.boxShadow='0 12px 35px rgba(255,182,0,0.5)';" onmouseout="this.style.transform='translateY(0) scale(1)'; this.style.boxShadow='0 8px 25px rgba(255,182,0,0.4)';">
                    <i class="fas fa-heart"></i> Donner Maintenant
                </a>
                <a href="index.php?controller=home&action=donation" style="background: transparent; color: #ffb600; padding: 12px 30px; border: 2px solid #ffb600; border-radius: 25px; text-decoration: none; font-weight: 700; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;" onmouseover="this.style.background='rgba(255,182,0,0.1)';" onmouseout="this.style.background='transparent';">
                    <i class="fas fa-info-circle"></i> En Savoir Plus
                </a>
            </div>
        </div>
        
        <div style="text-align: center;">
            <div style="background: rgba(255,182,0,0.1); border: 2px solid #ffb600; border-radius: 15px; padding: 25px; backdrop-filter: blur(10px);">
                <div style="font-size: 2.5rem; font-weight: 900; color: #ffb600; margin-bottom: 10px;">5,000+</div>
                <div style="color: rgba(255,255,255,0.9); font-size: 0.95rem; margin-bottom: 20px;">Vies transformées</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 0.85rem;">
                    <div style="color: rgba(255,255,255,0.8);">
                        <div style="font-weight: 700; color: #ffb600; margin-bottom: 5px;">1,200+</div>
                        <div>Bourses</div>
                    </div>
                    <div style="color: rgba(255,255,255,0.8);">
                        <div style="font-weight: 700; color: #ffb600; margin-bottom: 5px;">800+</div>
                        <div>Soins</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes rotate-3d {
        0% { transform: rotateX(0deg) rotateY(0deg) rotateZ(0deg); }
        100% { transform: rotateX(360deg) rotateY(360deg) rotateZ(360deg); }
    }
</style>
