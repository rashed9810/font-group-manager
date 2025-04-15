<?php
require_once 'config/config.php';
require_once 'utils/helpers.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Font Group System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Font Group System</h1>

        <!-- Font Upload Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>Upload Font</h2>
            </div>
            <div class="card-body">
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-cloud-arrow-up" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M7.646 5.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708l2-2z"/>
                            <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383zm.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"/>
                        </svg>
                    </div>
                    <p>Click to upload or drag and drop</p>
                    <p class="text-muted">Only TTF File Allowed</p>
                    <input type="file" id="fontFileInput" accept=".ttf" class="d-none">
                </div>
                <div id="uploadProgress" class="progress mt-3 d-none">
                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <!-- Font List Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>Our Fonts</h2>
                <p class="text-muted">Browse a list of fonts to build your font group.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>FONT NAME</th>
                                <th>PREVIEW</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="fontList">
                            <!-- Font list will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create Font Group Section -->
        <div class="card mb-4">
            <div class="card-header border-bottom-0 bg-white">
                <h2 class="mb-1">Create Font Group</h2>
                <p class="text-muted mb-0">You have to select at least two fonts</p>
            </div>
            <div class="card-body pt-0">
                <form id="fontGroupForm">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="groupTitle" placeholder="Group Title" required>
                    </div>

                    <div class="row mb-2 font-header-row">
                        <div class="col-auto"></div>
                        <div class="col-3">Font Name</div>
                        <div class="col-3">Select a Font</div>
                        <div class="col-2">Specific Size</div>
                        <div class="col-2">Price Change</div>
                        <div class="col-auto"></div>
                    </div>

                    <div id="fontRowsContainer">
                        <!-- Font rows will be added here -->
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <button type="button" id="addRowBtn" class="btn btn-outline-primary">+ Add Row</button>
                        <button type="submit" id="createGroupBtn" class="btn btn-success">Create</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Font Groups List Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h2>Our Font Groups</h2>
                <p class="text-muted">List of all available font groups.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>FONTS</th>
                                <th>COUNT</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody id="fontGroupList">
                            <!-- Font groups will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Font Group Modal -->
    <div class="modal fade" id="editGroupModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Font Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editGroupForm">
                        <input type="hidden" id="editGroupId">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="editGroupTitle" placeholder="Group Title" required>
                        </div>

                        <div class="row mb-2 font-header-row">
                            <div class="col-auto"></div>
                            <div class="col-3">Font Name</div>
                            <div class="col-3">Select a Font</div>
                            <div class="col-2">Specific Size</div>
                            <div class="col-2">Price Change</div>
                            <div class="col-auto"></div>
                        </div>

                        <div id="editFontRowsContainer">
                            <!-- Edit font rows will be populated here -->
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" id="editAddRowBtn" class="btn btn-outline-success">+ Add Row</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="saveGroupChangesBtn" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
