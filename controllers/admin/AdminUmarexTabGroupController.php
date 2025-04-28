<?php

/**
 * Contrôleur Admin pour gérer le groupe Umarex.
 *
 * Ce contrôleur affiche une page d'accueil pour le groupe Umarex dans le back-office.
 * Il peut être étendu pour ajouter des fonctionnalités spécifiques au groupe.
 *
 * @Copilot
 */
class AdminUmarexTabGroupController extends ModuleAdminController
{
    /**
     * Constructeur du contrôleur.
     * Initialise les propriétés de base et active le bootstrap.
     *
     * @Copilot
     */
    public function __construct()
    {
        parent::__construct();

        // Active le bootstrap pour un rendu moderne dans le back-office
        $this->bootstrap = true;
    }

    /**
     * Initialise le contenu de la page.
     *
     * Cette méthode ajoute un panneau d'accueil pour le groupe Umarex.
     *
     * @Copilot
     */
    public function initContent()
    {
        parent::initContent();

        // Ajoute un panneau d'accueil avec un message de bienvenue
        $this->content .= '<div class="panel">
            <h3><i class="material-icons">store</i> Welcome to the UMAREX group</h3>
            <p>This is a placeholder page. You can start adding modules under this group.</p>
        </div>';
    }
}
