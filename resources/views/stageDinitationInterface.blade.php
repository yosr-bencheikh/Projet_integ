@include('components.side-bar')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestion des Soutenances</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        .sidebar {
            background-color: #343a40;
            color: white;
            height: 100vh;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 5px 0;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .sidebar-header {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            padding: 15px 0;
            border-bottom: 1px solid #495057;
        }

        .main-content {
            margin-left: 0;
        }

        @media (min-width: 768px) {
            .main-content {
                margin-left: 200px;
            }

            .sidebar {
                width: 200px;
                position: fixed;
            }
        }

        .table-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .hover-effect {
            transition: background-color 0.3s;
        }

        .hover-effect:hover {
            background-color: #f1f1f1;
        }

        .btn-custom {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }

        .btn-group-fixed {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar d-none d-md-block">
                <div class="sidebar-header">
                    Menu
                </div>
                <a href="#" onclick="switchToMainInterface()"><i class="bi bi-house"></i> Accueil</a>
                <a href="#" onclick="switchToNotesInterface()"><i class="bi bi-journal"></i> Notes</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Interface principale -->
                <div class="container mt-4" id="mainInterface">
                    <div class="table-container">
                        <div class="header d-flex justify-content-between align-items-center">
                            <h2>Liste des Étudiants en Stage d'initiation</h2>
                            <button class="btn btn-print btn-custom" onclick="imprimerPDF()">
                                <i class="bi bi-printer"></i> Imprimer en PDF
                            </button>
                        </div>
                        <div class="search-bar">
                            <input type="text" class="form-control w-25" id="searchInput" placeholder="Rechercher..." onkeyup="rechercher()" />
                        </div>
                        <table class="table table-striped hover-effect">
                            <thead>
                                <tr>
                                    <th>Étudiant</th>
                                    <th>Jury 1</th>
                                    <th>Jury 2</th>
                                    <th>Société de Stage</th>
                                    <th>Durée de Stage</th>
                                    <th>Classe</th>
                                    <th>Heure</th>
                                    <th>Date de Soutenance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="soutenancesTable">
                                <!-- Les données seront ajoutées ici dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                    <!-- Boutons pour navigation -->
                    <div class="btn-group-fixed">
                        <button class="btn btn-primary btn-custom" onclick="openModal()">
                            <i class="bi bi-plus-circle"></i> Ajouter
                        </button>
                        <button class="btn btn-success btn-custom" onclick="switchToNotesInterface()">
                            <i class="bi bi-layout-text-sidebar-reverse"></i> Notes
                        </button>
                    </div>
                </div>

                <!-- Interface des notes -->
                <div class="container mt-4 hidden" id="notesInterface">
                    <div class="table-container">
                        <div class="header d-flex justify-content-between align-items-center">
                            <h2>Ajouter des Notes de Soutenance d'initiation</h2>
                            <button class="btn btn-print btn-custom" onclick="extraireListe()">
                                <i class="bi bi-printer"></i> Imprimer en PDF
                            </button>
                        </div>
                        <div class="search-bar">
                            <input type="text" class="form-control w-25" id="searchNotesInput" placeholder="Rechercher..." onkeyup="rechercherNotes()" />
                        </div>
                        <table class="table table-striped hover-effect">
                            <thead>
                                <tr>
                                    <th>Étudiant</th>
                                    <th>Jury 1</th>
                                    <th>Jury 2</th>
                                    <th>Société de Stage</th>
                                    <th>Note de Soutenance</th>
                                    <th>Validation</th>
                                </tr>
                            </thead>
                            <tbody id="notesTable">
                                <!-- Les données seront ajoutées ici dynamiquement -->
                            </tbody>
                        </table>
                    </div>
                    <!-- Bouton pour revenir à la liste principale -->
                    <div class="btn-group-fixed">
                        <button class="btn btn-secondary btn-custom" onclick="switchToMainInterface()">
                            <i class="bi bi-arrow-left-circle"></i> Retour
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour formulaire d'ajout -->
    <div class="modal fade" id="soutenanceModal" tabindex="-1" aria-labelledby="soutenanceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="soutenanceModalLabel">Ajouter / Modifier une Soutenance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="soutenanceForm">
                        <!-- Form inputs go here -->
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-primary" onclick="saveSoutenance()">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JS functions remain unchanged, including search, switch interfaces, etc.
    </script>
</body>

</html>