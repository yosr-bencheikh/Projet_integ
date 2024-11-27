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
      padding-bottom: 60px;
    }

    .table-container {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .hover-effect:hover {
      background-color: #f1f1f1;
    }

    .sidebar {
      background-color: #343a40;
      color: #fff;
      height: 100vh;
      padding: 20px;
    }

    .sidebar a {
      color: #fff;
      text-decoration: none;
      display: block;
      padding: 10px;
      margin-bottom: 5px;
      border-radius: 5px;
      transition: background-color 0.3s;
    }

    .sidebar a:hover {
      background-color: #495057;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
        @include('components.side-bar')
      </nav>

      <!-- Main content -->
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="container mt-4">
          <div class="table-container">
            <div class="header d-flex justify-content-between align-items-center">
              <h2>Liste des Soutenances En PFE</h2>
              <button class="btn btn-warning btn-sm" onclick="imprimerPDF()">
                <i class="bi bi-printer"></i> Imprimer en PDF
              </button>
            </div>
            <div class="search-bar my-3">
              <input type="text" class="form-control w-25" id="searchInput" placeholder="Rechercher..." onkeyup="rechercher()" />
            </div>
            <table class="table table-striped hover-effect">
              <thead>
                <tr>
                  <th>Étudiant</th>
                  <th>Encadreur</th>
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
                <!-- Dynamically added rows -->
              </tbody>
            </table>
          </div>

          <!-- Fixed Buttons -->
          <div class="btn-group-fixed d-flex justify-content-end my-3">
            <button class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#addModal">
              <i class="bi bi-plus-circle"></i> Ajouter
            </button>
            <button class="btn btn-success btn-sm" onclick="switchToNotesInterface()">
              <i class="bi bi-layout-text-sidebar-reverse"></i> Notes
            </button>
          </div>
        </div>
      </main>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addModalLabel">Ajouter une Soutenance</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addForm">
            <div class="mb-3">
              <label for="etudiant" class="form-label">Étudiant</label>
              <input type="text" class="form-control" id="etudiant" required />
            </div>
            <div class="mb-3">
              <label for="encadreur" class="form-label">Encadreur</label>
              <input type="text" class="form-control" id="encadreur" required />
            </div>
            <div class="mb-3">
              <label for="jury1" class="form-label">Jury 1</label>
              <input type="text" class="form-control" id="jury1" required />
            </div>
            <div class="mb-3">
              <label for="jury2" class="form-label">Jury 2</label>
              <input type="text" class="form-control" id="jury2" required />
            </div>
            <div class="mb-3">
              <label for="societe" class="form-label">Société de Stage</label>
              <input type="text" class="form-control" id="societe" required />
            </div>
            <div class="mb-3">
              <label for="duree" class="form-label">Durée de Stage</label>
              <input type="text" class="form-control" id="duree" required />
            </div>
            <div class="mb-3">
              <label for="classe" class="form-label">Classe</label>
              <input type="text" class="form-control" id="classe" required />
            </div>
            <div class="mb-3">
              <label for="heure" class="form-label">Heure</label>
              <input type="time" class="form-control" id="heure" required />
            </div>
            <div class="mb-3">
              <label for="date" class="form-label">Date de Soutenance</label>
              <input type="date" class="form-control" id="date" required />
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('addForm').addEventListener('submit', function(e) {
      e.preventDefault();

      // Get all form values
      const etudiant = document.getElementById('etudiant').value;
      const encadreur = document.getElementById('encadreur').value;
      const jury1 = document.getElementById('jury1').value;
      const jury2 = document.getElementById('jury2').value;
      const societe = document.getElementById('societe').value;
      const duree = document.getElementById('duree').value;
      const classe = document.getElementById('classe').value;
      const heure = document.getElementById('heure').value;
      const date = document.getElementById('date').value;

      // Create a new table row
      const table = document.getElementById('soutenancesTable');
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${etudiant}</td>
        <td>${encadreur}</td>
        <td>${jury1}</td>
        <td>${jury2}</td>
        <td>${societe}</td>
        <td>${duree}</td>
        <td>${classe}</td>
        <td>${heure}</td>
        <td>${date}</td>
        <td>
          <button class="btn btn-danger btn-sm" onclick="deleteRow(this)">Supprimer</button>
        </td>
      `;

      // Append the row to the table
      table.appendChild(row);

      // Reset the form
      document.getElementById('addForm').reset();

      // Close the modal
      const modal = bootstrap.Modal.getInstance(document.getElementById('addModal'));
      modal.hide();
    });

    // Function to delete a row
    function deleteRow(button) {
      button.parentElement.parentElement.remove();
    }
  </script>
</body>

</html>