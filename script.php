 let cart = [];
        let products = <?php echo json_encode($products); ?>;
        let adminAttempts = 0;
        const MAX_ADMIN_ATTEMPTS = 3;
        let blockedUntil = 0;

        // Initialisation au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            // Convertir les prix en nombres
            products = products.map(product => ({
                ...product,
                price: parseFloat(product.price)
            }));
            
            loadCart();
            setupEventListeners();
            
            // Attacher les événements aux images produits
            document.querySelectorAll('.product-image').forEach(image => {
                image.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    const product = products.find(p => p.id == productId);
                    if (product) {
                        openGallery(product);
                    }
                });
            });
            
            // Vérifier si l'admin est connecté
            <?php if (isset($_SESSION['admin_logged_in'])): ?>
                document.getElementById('adminPage').style.display = 'block';
            <?php endif; ?>
            
            // Initialiser le filtrage des produits
            initProductFiltering();
            
            // Zone secrète admin
            setupSecretAdminZone();
        });

        // Configurer la zone secrète admin
        function setupSecretAdminZone() {
            const secretZone = document.getElementById('secretAdminZone');
            let clickCount = 0;
            
            secretZone.addEventListener('click', function() {
                clickCount++;
                if(clickCount === 3) {
                    showAdminLogin();
                    clickCount = 0;
                }
                
                // Réinitialiser après 1 seconde
                setTimeout(() => { clickCount = 0; }, 1000);
            });
        }

        // Fonction pour initialiser le filtrage des produits
        function initProductFiltering() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Mettre à jour le bouton actif
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Récupérer la catégorie sélectionnée
                    const category = this.dataset.category;
                    
                    // Filtrer les produits
                    filterProducts(category);
                });
            });
        }

        // Fonction pour filtrer les produits par catégorie
        function filterProducts(category) {
            const productsContainer = document.getElementById('productsContainer');
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Fonction pour configurer les écouteurs d'événements
        function setupEventListeners() {
            // Bouton d'ajout au panier
            document.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('add-to-cart')) {
                    const productId = e.target.dataset.id;
                    const product = products.find(p => p.id == productId);
                    
                    if (product) {
                        addToCart(product);
                        updateCartUI();
                        showNotification(`${product.name} ajouté au panier!`, 'success');
                    }
                }
            });
            
            // Boutons du panier
            const cartIcon = document.getElementById('cartIcon');
            cartIcon.addEventListener('click', openCart);
            
            // Correction pour iOS
            cartIcon.addEventListener('touchend', function(e) {
                e.preventDefault();
                openCart();
            });

            document.getElementById('closeCart').addEventListener('click', closeCart);
            document.getElementById('overlay').addEventListener('click', closeCart);
            document.getElementById('continueShopping').addEventListener('click', closeCart);
            document.getElementById('checkoutBtn').addEventListener('click', checkout);
            
            // Administration
            document.getElementById('togglePassword').addEventListener('click', togglePasswordVisibility);
            if (document.getElementById('logoutAdmin')) {
                document.getElementById('logoutAdmin').addEventListener('click', logoutAdmin);
            }
            
            // Galerie d'images
            document.getElementById('closeGallery').addEventListener('click', closeGallery);
            document.getElementById('galleryPrev').addEventListener('click', showPrevImage);
            document.getElementById('galleryNext').addEventListener('click', showNextImage);
            
            // Prévisualisation des images pour l'admin
            const imagesInput = document.getElementById('images');
            if (imagesInput) {
                imagesInput.addEventListener('change', function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.innerHTML = '';
                    
                    for (const file of e.target.files) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'preview-image';
                            preview.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }
            
            // Boutons de modification des produits
            document.querySelectorAll('.edit-product').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.id;
                    const name = this.dataset.name;
                    const category = this.dataset.category;
                    const price = this.dataset.price;
                    const description = this.dataset.description;
                    const badge = this.dataset.badge;
                    
                    // Remplir le formulaire de modification
                    document.getElementById('update_product_id').value = productId;
                    document.getElementById('update_name').value = name;
                    document.getElementById('update_category').value = category;
                    document.getElementById('update_price').value = price;
                    document.getElementById('update_description').value = description;
                    document.getElementById('update_badge').value = badge || '';
                    
                    // Afficher le formulaire de modification
                    document.getElementById('updateProductForm').style.display = 'block';
                    
                    // Faire défiler jusqu'au formulaire
                    document.getElementById('updateProductForm').scrollIntoView({ behavior: 'smooth' });
                });
            });
            
            // Annuler la modification
            if (document.getElementById('cancelUpdate')) {
                document.getElementById('cancelUpdate').addEventListener('click', function() {
                    document.getElementById('updateProductForm').style.display = 'none';
                });
            }
        }

        // Fonctions pour le panier
        function addToCart(product) {
            const existingItem = cart.find(item => item.id == product.id);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: parseFloat(product.price),
                    image: product.images[0],
                    quantity: 1
                });
            }
            
            saveCart();
        }

        function removeFromCart(id) {
            cart = cart.filter(item => item.id != id);
            saveCart();
            updateCartUI();
        }

        function updateQuantity(id, quantity) {
            const item = cart.find(item => item.id == id);
            if (item) {
                item.quantity = parseInt(quantity);
                if (item.quantity <= 0) {
                    removeFromCart(id);
                } else {
                    saveCart();
                    updateCartUI();
                }
            }
        }

        function saveCart() {
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
        }

        function loadCart() {
            const savedCart = localStorage.getItem('cart');
            if (savedCart) {
                cart = JSON.parse(savedCart);
                
                // Convertir les prix en nombres dans le panier
                cart = cart.map(item => ({
                    ...item,
                    price: parseFloat(item.price)
                }));
                
                updateCartCount();
            }
        }

        function updateCartCount() {
            const count = cart.reduce((total, item) => total + item.quantity, 0);
            document.getElementById('cartCount').textContent = count;
        }

        function updateCartUI() {
            const cartItemsElement = document.getElementById('cartItems');
            const cartTotalElement = document.getElementById('cartTotal');
            
            if (cart.length === 0) {
                cartItemsElement.innerHTML = '<p>Votre panier est vide</p>';
                cartTotalElement.textContent = '0 DT';
                return;
            }
            
            let itemsHTML = '';
            let total = 0;
            
            cart.forEach(item => {
                // S'assurer que le prix est un nombre
                const price = parseFloat(item.price);
                const itemTotal = price * item.quantity;
                total += itemTotal;
                
                itemsHTML += `
                <div class="cart-item">
                    <img src="${item.image}" alt="${item.name}" class="cart-item-img">
                    <div class="cart-item-details">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">${price.toFixed(2)} DT x ${item.quantity}</div>
                    <div class="cart-item-actions">
                        <button class="quantity-btn minus" data-id="${item.id}">-</button>
                        <input type="number" class="item-quantity" value="${item.quantity}" min="1" data-id="${item.id}">
                        <button class="quantity-btn plus" data-id="${item.id}">+</button>
                        <button class="remove-item" data-id="${item.id}">
                        <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    </div>
                </div>
                `;
            });
            
            cartItemsElement.innerHTML = itemsHTML;
            cartTotalElement.textContent = `${total.toFixed(2)} DT`;
            
            // Ajouter les écouteurs d'événements pour les boutons du panier
            document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const item = cart.find(item => item.id == id);
                    if (item && item.quantity > 1) {
                        updateQuantity(id, item.quantity - 1);
                    }
                });
            });
            
            document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const item = cart.find(item => item.id == id);
                    if (item) {
                        updateQuantity(id, item.quantity + 1);
                    }
                });
            });
            
            document.querySelectorAll('.item-quantity').forEach(input => {
                input.addEventListener('change', function() {
                    const id = this.dataset.id;
                    updateQuantity(id, this.value);
                });
            });
            
            document.querySelectorAll('.remove-item').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    removeFromCart(id);
                });
            });
        }

        function openCart() {
            updateCartUI();
            document.getElementById('cartPanel').classList.add('open');
            document.getElementById('overlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeCart() {
            document.getElementById('cartPanel').classList.remove('open');
            document.getElementById('overlay').classList.remove('active');
            document.body.style.overflow = '';
        }

        function checkout() {
            if (cart.length === 0) {
                showNotification('Votre panier est vide!', 'error');
                return;
            }
            
            // Construire le message WhatsApp
            let message = "Bonjour, je souhaite commander les produits suivants :\n\n";
            let total = 0;
            
            cart.forEach(item => {
                const price = parseFloat(item.price);
                const itemTotal = price * item.quantity;
                total += itemTotal;
                message += `- ${item.name} (x${item.quantity}) : ${itemTotal.toFixed(2)} DT\n`;
            });
            
            message += `\nTotal : ${total.toFixed(2)} DT\n`;
            message += "Je confirme la commande avec paiement à la livraison.";
            
            // Sauvegarder la commande côté serveur
            saveOrderToServer(total, cart);
            
            // Ouvrir WhatsApp
            const whatsappNumber = "21628354929";
            const encodedMessage = encodeURIComponent(message);
            
            window.open(`https://wa.me/${whatsappNumber}?text=${encodedMessage}`, '_blank');
            
            // Vider le panier
            cart = [];
            saveCart();
            updateCartUI();
            updateCartCount();
            closeCart();
            
            // Notification de succès
            showNotification('Commande envoyée via WhatsApp!', 'success');
        }

        function saveOrderToServer(total, items) {
            // Envoyer les données au serveur via AJAX
            const data = {
                total: total,
                items: items.map(item => ({
                    id: item.id,
                    name: item.name,
                    price: item.price,
                    quantity: item.quantity
                }))
            };
            
            fetch('save_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (!result.success) {
                    console.error('Erreur lors de la sauvegarde de la commande');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        }

        // Fonctions d'administration
        function showAdminLogin() {
            const now = Date.now();
            if (blockedUntil > now) {
                const remainingTime = Math.ceil((blockedUntil - now) / 1000);
                showNotification(`Trop de tentatives. Réessayez dans ${remainingTime} secondes.`, 'error');
                return;
            }
            
            document.getElementById('adminLogin').style.display = 'block';
            document.getElementById('overlay').classList.add('active');
        }

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('adminPassword');
            const toggleIcon = document.getElementById('togglePassword').querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        function logoutAdmin() {
            window.location.href = '?logout=1';
        }

        // Fonction pour afficher les notifications
        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type} show`;
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        // Fonctions pour la galerie d'images
        let currentGalleryProduct = null;
        let currentImageIndex = 0;

        function openGallery(product) {
            if (!product || !product.images || product.images.length === 0) return;

            currentGalleryProduct = product;
            currentImageIndex = 0;
            
            // Afficher la première image
            document.getElementById('galleryMainImage').src = product.images[0];
            
            // Créer les miniatures
            const thumbnailsContainer = document.getElementById('galleryThumbnails');
            thumbnailsContainer.innerHTML = '';
            
            product.images.forEach((image, index) => {
                const thumbnail = document.createElement('img');
                thumbnail.src = image;
                thumbnail.className = 'gallery-thumbnail' + (index === 0 ? ' active' : '');
                thumbnail.dataset.index = index;
                thumbnail.addEventListener('click', () => {
                    showImage(index);
                });
                thumbnailsContainer.appendChild(thumbnail);
            });
            
            // Afficher la galerie
            document.getElementById('galleryModal').classList.add('active');
            document.getElementById('overlay').classList.add('active');
        }

        function closeGallery() {
            document.getElementById('galleryModal').classList.remove('active');
            document.getElementById('overlay').classList.remove('active');
        }

        function showImage(index) {
            if (!currentGalleryProduct || index < 0 || index >= currentGalleryProduct.images.length) return;
            
            currentImageIndex = index;
            document.getElementById('galleryMainImage').src = currentGalleryProduct.images[index];
            
            // Mettre à jour les miniatures actives
            document.querySelectorAll('.gallery-thumbnail').forEach((thumb, i) => {
                if (i === index) {
                    thumb.classList.add('active');
                } else {
                    thumb.classList.remove('active');
                }
            });
        }

        function showPrevImage() {
            if (!currentGalleryProduct) return;
            
            const newIndex = (currentImageIndex - 1 + currentGalleryProduct.images.length) % currentGalleryProduct.images.length;
            showImage(newIndex);
        }

        function showNextImage() {
            if (!currentGalleryProduct) return;
            
            const newIndex = (currentImageIndex + 1) % currentGalleryProduct.images.length;
            showImage(newIndex);
        }