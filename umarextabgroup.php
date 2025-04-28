<?php

// Fichier principal du module UmarexGroup
// @By Copilot

if (! defined('_PS_VERSION_')) {
    exit;
}

/**
 * Classe principale du module UmarexGroup.
 * @By Copilot
 */
class UmarexTabGroup extends Module
{
    /** @var string Nom de la classe du controller */
    public $tabClassName       = 'AdminUmarexTabGroup';
    public $parentTabClassName = 'AdminDashboard';

    /** @var string Nom technique du module */
    public $name = 'umarextabgroup';

    /** @var string Groupe d'affichage dans le BO */
    public $tab = 'administration';

    /** @var string Version du module */
    public $version = '1.0.0';

    /** @var string Auteur du module */
    public $author = 'Umarex-France';

    /** @var int Indique si une instance front est nécessaire */
    public $need_instance = 0;

    /** @var bool Active le bootstrap */
    public $bootstrap = true;

    /**
     * Constructeur du module.
     * Initialise les propriétés et les textes affichés.
     * @By Copilot
     */
    public function __construct()
    {
        parent::__construct();

        $this->displayName = $this->l('Umarex Tab Group');
        $this->description = $this->l('Module pour gérer le groupe d\'onglets Umarex dans le back-office.');
    }

    /**
     * Vérifie la compatibilité du module avec la version actuelle de PrestaShop.
     * @return bool Retourne true si compatible, sinon false.
     * @By Copilot
     */
    private function _isCompatibleWithPrestaShop(): bool
    {
        // Récupère la version actuelle de PrestaShop
        $currentVersion = _PS_VERSION_;

        // Définit la version minimale requise
        $minVersion = '8.2.0';

        // Vérifie si la version actuelle est compatible
        return version_compare($currentVersion, $minVersion, '>=');
    }

    /**
     * Installation du module avec vérification de compatibilité.
     * @return bool Retourne true si l'installation réussit, sinon false.
     * @By Copilot
     */
    public function install()
    {
        // Vérifie la compatibilité avec PrestaShop
        if (! $this->_isCompatibleWithPrestaShop()) {
            // Affiche un message d'erreur si incompatible
            $this->_errors[] = $this->l('Ce module nécessite PrestaShop 8.2 ou une version ultérieure.');
            return false;
        }

        // Appelle la méthode d'installation parente et installe les onglets
        return parent::install() && $this->_installTabs();
    }

    /**
     * Désinstallation du module.
     * Supprime les onglets d'administration.
     * @return bool
     * @By Copilot
     */
    public function uninstall()
    {
        return parent::uninstall() && $this->_uninstallTabs();
    }

    /**
     * Crée les onglets (menu) dans le back-office :
     * ADMINISTRATION > Umarex > Umarex Group
     * @return bool
     * @By Copilot
     */
    private function _installTabs()
    {
        // Le module a déjà été installé, on ne fait rien
        $tab_id = Tab::getIdFromClassName($this->tabClassName);
        if ((bool) $tab_id) {
            return true;
        }

        // Création
        $tab             = new Tab();
        $tab->id_parent  = 0; // Tab::getIdFromClassName($this->parentTabClassName);
        $tab->position   = 1;
        $tab->class_name = $this->tabClassName;
        $tab->module     = $this->name;
        $tab->active     = 1;

        // ✅ Ajout du wording Symfony (nouveau système)
        $tab->wording        = 'UmarexTabGroup';
        $tab->wording_domain = 'Modules.UmarexTabGroup.Admin';

        // ✅ Ajout du name[] pour compatibilité legacy
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Umarex';
        }

        return $tab->add();
    }

    /**
     * Supprime tous les onglets créés par ce module.
     * @return bool
     * @By Copilot
     */
    private function _uninstallTabs()
    {
        // L'ID de la tab groupe "Umarex"
        $tab_id = Tab::getIdFromClassName($this->tabClassName);

        // Zéro est l'ID de la tab "AdminDashboard"
        if ((bool) $tab_id) {
            /// Recherche des enfants
            $sql   = 'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'tab` WHERE `id_parent` = ' . (int) $tab_id;
            $count = (int) Db::getInstance()->getValue($sql);

            // S'il reste au moins un enfant, on affiche un message et on ne supprime pas le groupe
            if ($count > 0) {
                $this->_errors[] = $this->l('Impossible de supprimer le groupe Umarex car il contient encore des sous-onglets.'); // @Copilot
                return false;
            }
            // Suppression de la tab groupe "Umarex"
            $tab = new Tab($tab_id);
            $tab->delete();
        }

        return true;
    }

    /**
     * Redirige la configuration du module vers le contrôleur Admin personnalisé.
     * @return void
     * @By Copilot
     */
    public function getContent()
    {
        Tools::redirectAdmin('index.php?controller=AdminUmarexGroup&token=' . Tools::getAdminTokenLite('AdminUmarexGroupController'));
    }

    /**
     * Retourne l'ID d'un onglet via le service Symfony (PrestaShop 8.2+ compliant).
     * @param string $className
     * @return int|null
     * @By Copilot
     */
    private function _getTabIdByClassName(string $className): ?int
    {
        /** @var \PrestaShop\PrestaShop\Core\Admin\Tab\Repository\TabRepository $tabRepo */
        $tabRepo = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()
            ->get('prestashop.core.admin.tab.repository');

        $tab = $tabRepo->findOneByClassName($className);
        return $tab ? (int) $tab->getId() : null;
    }
}
