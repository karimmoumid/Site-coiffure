# 💇‍♀️ Salon Sana - Système de Gestion de Salon de Coiffure

![Symfony](https://img.shields.io/badge/Symfony-000000?style=for-the-badge&logo=symfony&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

## 📋 Description

Salon Sana est une application web complète de gestion de salon de coiffure développée avec Symfony. Elle offre un système de prise de rendez-vous en ligne, une gestion des services, un système d'administration complet et bien plus encore.

## ✨ Fonctionnalités Principales

### 👥 Côté Client
- 📅 **Prise de rendez-vous en ligne** avec sélection de services et créneaux horaires
- 🔍 **Consultation des services** par catégories avec descriptions et tarifs
- 📧 **Système de contact** avec formulaire de contact
- 📱 **Interface responsive** adaptée à tous les appareils
- 🔐 **Espace client** avec authentification sécurisée

### 🎯 Côté Administration
- 📊 **Dashboard administrateur** avec statistiques en temps réel
- 👤 **Gestion des utilisateurs** (Admin, Employé)
- ✂️ **Gestion des services** et catégories
- 📅 **Gestion des rendez-vous** (confirmation, annulation, suivi)
- 📦 **Gestion du stock** de produits
- 📧 **Notifications email automatiques**

### 🔒 Sécurité et Conformité
- 🛡️ **Authentification sécurisée** avec hashage des mots de passe
- 👮 **Système de rôles** (ROLE_ADMIN, ROLE_EMPLOYEE, ROLE_USER)
- 📜 **Conformité RGPD** avec politique de confidentialité
- 🔐 **Protection CSRF** sur tous les formulaires

## 🛠️ Technologies Utilisées

### Backend
- **Symfony 7.4** - Framework PHP
- **Doctrine ORM** - Gestion de base de données
- **Twig** - Moteur de templates
- **Symfony Mailer** - Envoi d'emails
- **Symfony Security** - Authentification et autorisation

### Frontend
- **Bootstrap 5.3** - Framework CSS
- **Bootstrap Icons** - Icônes
- **Google Fonts** - Polices (Playfair Display, Poppins)
- **JavaScript** - Interactions dynamiques

### Base de données
- **MySQL/PostgreSQL** - Système de gestion de base de données
- **Doctrine Migrations** - Gestion des migrations

## 📁 Structure du Projet

```
salon-sana/
├── config/              # Configuration Symfony
│   ├── packages/       # Configuration des packages
│   └── routes/         # Configuration des routes
├── migrations/         # Migrations de base de données
├── public/            # Fichiers publics
│   └── uploads/       # Images uploadées
│       ├── categories/
│       └── services/
├── src/
│   ├── Controller/    # Controllers
│   │   ├── Admin/    # Controllers administration
│   │   └── Api/      # Controllers API
│   ├── Entity/       # Entités Doctrine
│   ├── Form/         # Formulaires Symfony
│   ├── Repository/   # Repositories Doctrine
│   └── Twig/        # Extensions Twig
├── templates/        # Templates Twig
│   ├── admin/       # Templates administration
│   ├── appointment/ # Templates rendez-vous
│   ├── emails/      # Templates emails
│   ├── home/        # Templates accueil
│   ├── legal/       # Templates pages légales
│   └── service/     # Templates services
├── .env             # Variables d'environnement
└── composer.json    # Dépendances PHP
```

## 🚀 Installation

### Prérequis
- PHP 8.1 ou supérieur
- Composer
- MySQL ou PostgreSQL
- Serveur web (Apache/Nginx)
- Node.js et npm (pour les assets)

### Étapes d'installation

1. **Cloner le repository**
```bash
git clone https://github.com/votre-username/salon-sana.git
cd salon-sana
```

2. **Installer les dépendances PHP**
```bash
composer install
```

3. **Configurer l'environnement**
```bash
cp .env .env.local
# Éditer .env.local et configurer les variables
```

4. **Configurer la base de données**
```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/salon_sana?serverVersion=8.0"
```

5. **Créer la base de données**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

6. **Charger les fixtures (optionnel)**
```bash
php bin/console doctrine:fixtures:load
```

7. **Créer les dossiers d'upload**
```bash
mkdir -p public/uploads/categories
mkdir -p public/uploads/services
chmod -R 775 public/uploads
```

8. **Configurer le serveur mail**
```env
MAILER_DSN=smtp://user:password@smtp.example.com:587
```

9. **Lancer le serveur de développement**
```bash
symfony server:start
# ou
php bin/console server:run
```

10. **Accéder à l'application**
- Site public : http://localhost:8000
- Administration : http://localhost:8000/admin/dashboard
- Connexion : http://localhost:8000/login

## 👤 Gestion des Utilisateurs

### Rôles disponibles
- **ROLE_ADMIN** : Accès complet à toutes les fonctionnalités
- **ROLE_EMPLOYEE** : Gestion des rendez-vous et du stock
- **ROLE_USER** : Client standard

### Créer un administrateur
```bash
php bin/console make:user:admin
# ou via l'interface après avoir créé le premier admin
```

## 📧 Configuration Email

L'application envoie des emails pour :
- Confirmation de rendez-vous au client
- Notification de nouveau rendez-vous à l'admin
- Confirmation/Annulation de rendez-vous
- Formulaire de contact

Configurer dans `.env.local` :
```env
MAILER_DSN=smtp://username:password@smtp.gmail.com:587
```

## 🗄️ Base de Données

### Entités principales
- **User** : Utilisateurs du système
- **Service** : Services proposés
- **ServiceCategory** : Catégories de services
- **Appointement** : Rendez-vous
- **Product** : Produits en stock
- **ProductCategory** : Catégories de produits
- **Image** : Images associées

### Migrations
```bash
# Créer une nouvelle migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate
```

## 🎨 Personnalisation

### Couleurs principales
Les couleurs sont définies dans `templates/base.html.twig` :
```css
--gold: #FFD700;
--dark-gold: #DAA520;
--black: #000000;
--dark-gray: #1a1a1a;
--light-gray: #f8f9fa;
```

### Logo et images
Placer vos images dans :
- `/public/uploads/categories/` pour les catégories
- `/public/uploads/services/` pour les services

## 🔧 Configuration

### Horaires d'ouverture
Modifier dans `src/Controller/Api/AppointmentApiController.php` :
```php
$openingTime->setTime(11, 0);  // 11h00
$closingTime->setTime(23, 59); // 23h59
```

### Informations du salon
Modifier dans les templates et la base de données :
- Adresse : Amal 2, Agadir 80000, Maroc
- Téléphone : +212 6 41 86 96 78
- Email : moumidmounir@gmail.com

## 📱 API Endpoints

### Rendez-vous
- `POST /api/appointment/available-slots` - Créneaux disponibles
- `POST /api/appointment/check-conflict` - Vérifier les conflits
- `POST /api/appointment/calculate-duration` - Calculer la durée totale

### Services
- `GET /api/services` - Liste des services

## 🧪 Tests

```bash
# Lancer les tests
php bin/phpunit

# Avec couverture
php bin/phpunit --coverage-html coverage
```

## 🚢 Déploiement

### Production
1. Configurer les variables d'environnement de production
2. Installer les dépendances sans dev :
```bash
composer install --no-dev --optimize-autoloader
```

3. Clear et warm up cache :
```bash
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod
```

4. Configurer le serveur web (Apache/Nginx)

### Configuration Apache
```apache
<VirtualHost *:80>
    ServerName salon-sana.com
    DocumentRoot /var/www/salon-sana/public
    
    <Directory /var/www/salon-sana/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

## 📄 Licence

Ce projet est sous licence propriétaire. Tous droits réservés.

## 👥 Équipe

- **Développement** : MOUMID Karim
- **Design** : MOUMID Karim
- **Maintenance** : MOUMID Karim

## 🙏 Remerciements

- Symfony pour le framework robuste
- Bootstrap pour le design responsive
- La communauté open source pour les nombreux packages utilisés

---

© 2024 Salon Sana. Tous droits réservés.
