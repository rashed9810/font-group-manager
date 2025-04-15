document.addEventListener("DOMContentLoaded", function () {
  initApp();
});
function initApp() {
  initFontUploader();
  loadFonts();
  loadFontGroups();
  initFontGroupForm();
}
function initFontUploader() {
  const uploadArea = document.getElementById("uploadArea");
  const fileInput = document.getElementById("fontFileInput");
  const progressBar = document.querySelector("#uploadProgress .progress-bar");
  const progressContainer = document.getElementById("uploadProgress");
  uploadArea.addEventListener("click", function () {
    fileInput.click();
  });
  uploadArea.addEventListener("dragover", function (e) {
    e.preventDefault();
    uploadArea.classList.add("dragover");
  });
  uploadArea.addEventListener("dragleave", function () {
    uploadArea.classList.remove("dragover");
  });
  uploadArea.addEventListener("drop", function (e) {
    e.preventDefault();
    uploadArea.classList.remove("dragover");
    if (e.dataTransfer.files.length) {
      handleFileUpload(e.dataTransfer.files[0]);
    }
  });
  fileInput.addEventListener("change", function () {
    if (fileInput.files.length) {
      handleFileUpload(fileInput.files[0]);
    }
  });
  function handleFileUpload(file) {
    if (!file.name.toLowerCase().endsWith(".ttf")) {
      alert("Only TTF files are allowed!");
      return;
    }
    progressContainer.classList.remove("d-none");
    progressBar.style.width = "0%";
    const formData = new FormData();
    formData.append("font_file", file);
    const xhr = new XMLHttpRequest();
    xhr.upload.addEventListener("progress", function (e) {
      if (e.lengthComputable) {
        const percentComplete = (e.loaded / e.total) * 100;
        progressBar.style.width = percentComplete + "%";
      }
    });
    xhr.onload = function () {
      if (xhr.status === 200) {
        try {
          const response = JSON.parse(xhr.responseText);
          if (response.success) {
            fileInput.value = "";
            setTimeout(function () {
              progressContainer.classList.add("d-none");
            }, 1000);
            loadFonts();
          } else {
            alert("Error: " + response.message);
          }
        } catch (e) {
          alert("Error processing response");
        }
      } else {
        alert("Upload failed. Please try again.");
      }
    };
    xhr.onerror = function () {
      alert("Upload failed. Please try again.");
    };
    xhr.open("POST", "api/upload_font.php", true);
    xhr.send(formData);
  }
}
function loadFonts() {
  const fontList = document.getElementById("fontList");
  fontList.innerHTML =
    '<tr><td colspan="3" class="text-center">Loading fonts...</td></tr>';
  fetch("api/get_fonts.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        fontList.innerHTML = "";
        if (data.fonts.length === 0) {
          fontList.innerHTML =
            '<tr><td colspan="3" class="text-center">No fonts uploaded yet.</td></tr>';
          return;
        }
        data.fonts.forEach((font) => {
          const row = document.createElement("tr");
          const fontFace = new FontFace(font.name, `url(${font.file_path})`);
          fontFace
            .load()
            .then(function (loadedFace) {
              document.fonts.add(loadedFace);
            })
            .catch(function (error) {
              console.error("Font loading failed:", error);
            });
          const nameCell = document.createElement("td");
          nameCell.textContent = font.name;
          const previewCell = document.createElement("td");
          const previewSpan = document.createElement("span");
          previewSpan.className = "font-preview";
          previewSpan.textContent = "Example Style";
          previewCell.appendChild(previewSpan);
          const actionCell = document.createElement("td");
          const deleteButton = document.createElement("button");
          deleteButton.className = "btn btn-sm btn-danger delete-font";
          deleteButton.setAttribute("data-id", font.id);
          deleteButton.textContent = "Delete";
          actionCell.appendChild(deleteButton);
          row.appendChild(nameCell);
          row.appendChild(previewCell);
          row.appendChild(actionCell);
          const styleElement = document.createElement("style");
          styleElement.textContent = ` @font-face { font-family: '${font.name}'; src: url('${font.file_path}') format('truetype'); font-weight: normal; font-style: normal; } `;
          document.head.appendChild(styleElement);
          fontFace
            .load()
            .then(() => {
              previewSpan.style.fontFamily = `'${font.name}', sans-serif`;
              previewSpan.style.opacity = "0.99";
              setTimeout(() => {
                previewSpan.style.opacity = "1";
              }, 50);
            })
            .catch((error) => {
              console.error(`Failed to load font ${font.name}:`, error);
            });
          fontList.appendChild(row);
        });
        updateFontSelects(data.fonts);
        document.querySelectorAll(".delete-font").forEach((button) => {
          button.addEventListener("click", function () {
            const fontId = this.getAttribute("data-id");
            deleteFont(fontId);
          });
        });
      } else {
        fontList.innerHTML = `<tr><td colspan="3" class="text-center text-danger">${data.message}</td></tr>`;
      }
    })
    .catch((error) => {
      console.error("Error loading fonts:", error);
      fontList.innerHTML =
        '<tr><td colspan="3" class="text-center text-danger">Failed to load fonts. Please try again.</td></tr>';
    });
}
function updateFontSelects(fonts) {
  const fontSelects = document.querySelectorAll(".font-select");
  fontSelects.forEach((select) => {
    const currentValue = select.value;
    while (select.options.length > 1) {
      select.remove(1);
    }
    fonts.forEach((font) => {
      const option = document.createElement("option");
      option.value = font.id;
      option.textContent = font.name;
      select.appendChild(option);
    });
    if (currentValue) {
      select.value = currentValue;
    }
  });
}
function deleteFont(fontId) {
  if (!confirm("Are you sure you want to delete this font?")) {
    return;
  }
  const formData = new FormData();
  formData.append("font_id", fontId);
  fetch("api/delete_font.php", { method: "POST", body: formData })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        loadFonts();
        loadFontGroups();
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error deleting font:", error);
      alert("Failed to delete font. Please try again.");
    });
}
function initFontGroupForm() {
  const addRowBtn = document.getElementById("addRowBtn");
  const fontRowsContainer = document.getElementById("fontRowsContainer");
  const fontGroupForm = document.getElementById("fontGroupForm");
  addRowBtn.addEventListener("click", function () {
    addFontRow(fontRowsContainer);
  });
  fontGroupForm.addEventListener("submit", function (e) {
    e.preventDefault();
    createFontGroup();
  });
  addFontRow(fontRowsContainer);
  initEditModal();
}
function addFontRow(container) {
  const row = document.createElement("div");
  row.className = "font-row";
  row.innerHTML = ` <div class="row align-items-center"> <div class="col-auto pr-0"> <span class="drag-handle"> <svg width="16" height="24" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <path d="M12 3v18"/> <path d="M5 8l7-5 7 5"/> <path d="M5 16l7 5 7-5"/> </svg> </span> </div> <div class="col-3"> <input type="text" class="form-control font-name" placeholder="Font Name" readonly> </div> <div class="col-3"> <select class="form-select font-select" required> <option value="">Select a Font</option> </select> </div> <div class="col-2"> <div class="size-input"> <div class="input-group"> <input type="text" class="form-control size-value" value="1.00"> <div class="input-group-append"> <div class="dropdown-arrows"> <button type="button" class="arrow-up size-up"> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <polyline points="6 15 12 9 18 15" /> </svg> </button> <button type="button" class="arrow-down size-down"> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <polyline points="6 9 12 15 18 9" /> </svg> </button> </div> </div> </div> </div> </div> <div class="col-2"> <div class="price-input"> <div class="input-group"> <input type="text" class="form-control price-value" value="0"> <div class="input-group-append"> <div class="dropdown-arrows"> <button type="button" class="arrow-up price-up"> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <polyline points="6 15 12 9 18 15" /> </svg> </button> <button type="button" class="arrow-down price-down"> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <polyline points="6 9 12 15 18 9" /> </svg> </button> </div> </div> </div> </div> </div> <div class="col-auto pl-0"> <button type="button" class="remove-row">×</button> </div> </div> `;
  container.appendChild(row);
  fetch("api/get_fonts.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const select = row.querySelector(".font-select");
        data.fonts.forEach((font) => {
          const option = document.createElement("option");
          option.value = font.id;
          option.textContent = font.name;
          select.appendChild(option);
        });
        select.addEventListener("change", function () {
          const fontName = row.querySelector(".font-name");
          const selectedOption = this.options[this.selectedIndex];
          if (selectedOption.value) {
            fontName.value = selectedOption.textContent;
          } else {
            fontName.value = "";
          }
        });
      }
    });
  row.querySelector(".remove-row").addEventListener("click", function () {
    if (container.children.length > 2) {
      container.removeChild(row);
    } else {
      alert("You need at least 2 fonts to create a group.");
    }
  });
  row.querySelector(".size-up").addEventListener("click", function () {
    const sizeInput = row.querySelector(".size-value");
    let value = parseFloat(sizeInput.value);
    value = (Math.round((value + 0.01) * 100) / 100).toFixed(2);
    sizeInput.value = value;
  });
  row.querySelector(".size-down").addEventListener("click", function () {
    const sizeInput = row.querySelector(".size-value");
    let value = parseFloat(sizeInput.value);
    if (value > 0) {
      value = (Math.round((value - 0.01) * 100) / 100).toFixed(2);
      sizeInput.value = value;
    }
  });
  row.querySelector(".price-up").addEventListener("click", function () {
    const priceInput = row.querySelector(".price-value");
    let value = parseInt(priceInput.value);
    priceInput.value = value + 1;
  });
  row.querySelector(".price-down").addEventListener("click", function () {
    const priceInput = row.querySelector(".price-value");
    let value = parseInt(priceInput.value);
    if (value > 0) {
      priceInput.value = value - 1;
    }
  });
  row.querySelector(".size-value").addEventListener("change", function () {
    let value = parseFloat(this.value);
    if (isNaN(value) || value < 0) {
      value = 1.0;
    }
    this.value = value.toFixed(2);
  });
  row.querySelector(".price-value").addEventListener("change", function () {
    let value = parseInt(this.value);
    if (isNaN(value) || value < 0) {
      value = 0;
    }
    this.value = value;
  });
}
function createFontGroup() {
  const groupTitle = document.getElementById("groupTitle").value;
  let selectedFonts = 0;
  const fontData = [];
  const fontRows = document.querySelectorAll("#fontRowsContainer .font-row");
  fontRows.forEach((row) => {
    const select = row.querySelector(".font-select");
    if (select.value) {
      selectedFonts++;
      const sizeValue = row.querySelector(".size-value").value;
      const priceValue = row.querySelector(".price-value").value;
      fontData.push({ id: select.value, size: sizeValue, price: priceValue });
    }
  });
  if (selectedFonts < 2) {
    alert("You need to select at least 2 fonts to create a group.");
    return;
  }
  const formData = new FormData();
  formData.append("title", groupTitle);
  formData.append("font_data", JSON.stringify(fontData));
  fetch("api/create_font_group.php", { method: "POST", body: formData })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.getElementById("fontGroupForm").reset();
        const container = document.getElementById("fontRowsContainer");
        while (container.children.length > 1) {
          container.removeChild(container.lastChild);
        }
        container.querySelector(".font-name").value = "";
        loadFontGroups();
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error creating font group:", error);
      alert("Failed to create font group. Please try again.");
    });
}
function loadFontGroups() {
  const fontGroupList = document.getElementById("fontGroupList");
  fontGroupList.innerHTML =
    '<tr><td colspan="4" class="text-center">Loading font groups...</td></tr>';
  fetch("api/get_font_groups.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        fontGroupList.innerHTML = "";
        if (data.font_groups.length === 0) {
          fontGroupList.innerHTML =
            '<tr><td colspan="4" class="text-center">No font groups created yet.</td></tr>';
          return;
        }
        data.font_groups.forEach((group) => {
          const row = document.createElement("tr");
          row.innerHTML = ` <td>${group.title}</td> <td>${group.fonts
            .map((font) => font.name)
            .join(", ")}</td> <td>${
            group.fonts.length
          }</td> <td> <button class="btn btn-sm btn-primary edit-group" data-id="${
            group.id
          }">Edit</button> <button class="btn btn-sm btn-danger delete-group" data-id="${
            group.id
          }">Delete</button> </td> `;
          fontGroupList.appendChild(row);
        });
        document.querySelectorAll(".edit-group").forEach((button) => {
          button.addEventListener("click", function () {
            const groupId = this.getAttribute("data-id");
            openEditModal(groupId);
          });
        });
        document.querySelectorAll(".delete-group").forEach((button) => {
          button.addEventListener("click", function () {
            const groupId = this.getAttribute("data-id");
            deleteFontGroup(groupId);
          });
        });
      } else {
        fontGroupList.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${data.message}</td></tr>`;
      }
    })
    .catch((error) => {
      console.error("Error loading font groups:", error);
      fontGroupList.innerHTML =
        '<tr><td colspan="4" class="text-center text-danger">Failed to load font groups. Please try again.</td></tr>';
    });
}
function initEditModal() {
  const editAddRowBtn = document.getElementById("editAddRowBtn");
  const editFontRowsContainer = document.getElementById(
    "editFontRowsContainer"
  );
  const saveGroupChangesBtn = document.getElementById("saveGroupChangesBtn");
  editAddRowBtn.addEventListener("click", function () {
    addFontRow(editFontRowsContainer);
  });
  saveGroupChangesBtn.addEventListener("click", function () {
    updateFontGroup();
  });
}
function openEditModal(groupId) {
  const editGroupId = document.getElementById("editGroupId");
  const editGroupTitle = document.getElementById("editGroupTitle");
  const editFontRowsContainer = document.getElementById(
    "editFontRowsContainer"
  );
  editGroupId.value = groupId;
  editFontRowsContainer.innerHTML = "";
  fetch(`api/get_font_groups.php?id=${groupId}`)
    .then((response) => response.json())
    .then((data) => {
      if (data.success && data.font_groups.length > 0) {
        const group = data.font_groups[0];
        editGroupTitle.value = group.title;
        group.fonts.forEach((font) => {
          const row = document.createElement("div");
          row.className = "font-row";
          row.innerHTML = ` <div class="row align-items-center"> <div class="col-auto pr-0"> <span class="drag-handle"> <svg width="16" height="24" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <path d="M12 3v18"/> <path d="M5 8l7-5 7 5"/> <path d="M5 16l7 5 7-5"/> </svg> </span> </div> <div class="col-3"> <input type="text" class="form-control font-name" placeholder="Font Name" readonly value="${font.name}"> </div> <div class="col-3"> <select class="form-select font-select" required> <option value="">Select a Font</option> </select> </div> <div class="col-2"> <div class="size-input"> <div class="input-group"> <input type="text" class="form-control size-value" value="1.00"> <div class="input-group-append"> <div class="dropdown-arrows"> <button type="button" class="arrow-up size-up"> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <polyline points="6 15 12 9 18 15" /> </svg> </button> <button type="button" class="arrow-down size-down"> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <polyline points="6 9 12 15 18 9" /> </svg> </button> </div> </div> </div> </div> </div> <div class="col-2"> <div class="price-input"> <div class="input-group"> <input type="text" class="form-control price-value" value="0"> <div class="input-group-append"> <div class="dropdown-arrows"> <button type="button" class="arrow-up price-up"> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <polyline points="6 15 12 9 18 15" /> </svg> </button> <button type="button" class="arrow-down price-down"> <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <polyline points="6 9 12 15 18 9" /> </svg> </button> </div> </div> </div> </div> </div> <div class="col-auto pl-0"> <button type="button" class="remove-row">×</button> </div> </div> `;
          editFontRowsContainer.appendChild(row);
          fetch("api/get_fonts.php")
            .then((response) => response.json())
            .then((fontData) => {
              if (fontData.success) {
                const select = row.querySelector(".font-select");
                fontData.fonts.forEach((fontOption) => {
                  const option = document.createElement("option");
                  option.value = fontOption.id;
                  option.textContent = fontOption.name;
                  if (fontOption.id === font.id) {
                    option.selected = true;
                  }
                  select.appendChild(option);
                });
                select.addEventListener("change", function () {
                  const fontName = row.querySelector(".font-name");
                  const selectedOption = this.options[this.selectedIndex];
                  if (selectedOption.value) {
                    fontName.value = selectedOption.textContent;
                  } else {
                    fontName.value = "";
                  }
                });
              }
            });
          row
            .querySelector(".remove-row")
            .addEventListener("click", function () {
              if (editFontRowsContainer.children.length > 2) {
                editFontRowsContainer.removeChild(row);
              } else {
                alert("You need at least 2 fonts in a group.");
              }
            });
          row.querySelector(".size-up").addEventListener("click", function () {
            const sizeInput = row.querySelector(".size-value");
            let value = parseFloat(sizeInput.value);
            value = (Math.round((value + 0.01) * 100) / 100).toFixed(2);
            sizeInput.value = value;
          });
          row
            .querySelector(".size-down")
            .addEventListener("click", function () {
              const sizeInput = row.querySelector(".size-value");
              let value = parseFloat(sizeInput.value);
              if (value > 0) {
                value = (Math.round((value - 0.01) * 100) / 100).toFixed(2);
                sizeInput.value = value;
              }
            });
          row.querySelector(".price-up").addEventListener("click", function () {
            const priceInput = row.querySelector(".price-value");
            let value = parseInt(priceInput.value);
            priceInput.value = value + 1;
          });
          row
            .querySelector(".price-down")
            .addEventListener("click", function () {
              const priceInput = row.querySelector(".price-value");
              let value = parseInt(priceInput.value);
              if (value > 0) {
                priceInput.value = value - 1;
              }
            });
          row
            .querySelector(".size-value")
            .addEventListener("change", function () {
              let value = parseFloat(this.value);
              if (isNaN(value) || value < 0) {
                value = 1.0;
              }
              this.value = value.toFixed(2);
            });
          row
            .querySelector(".price-value")
            .addEventListener("change", function () {
              let value = parseInt(this.value);
              if (isNaN(value) || value < 0) {
                value = 0;
              }
              this.value = value;
            });
        });
        const modal = new bootstrap.Modal(
          document.getElementById("editGroupModal")
        );
        modal.show();
      } else {
        alert("Error: " + (data.message || "Failed to load font group data."));
      }
    })
    .catch((error) => {
      console.error("Error loading font group data:", error);
      alert("Failed to load font group data. Please try again.");
    });
}
function updateFontGroup() {
  const groupId = document.getElementById("editGroupId").value;
  const groupTitle = document.getElementById("editGroupTitle").value;
  let selectedFonts = 0;
  const fontData = [];
  const fontRows = document.querySelectorAll(
    "#editFontRowsContainer .font-row"
  );
  fontRows.forEach((row) => {
    const select = row.querySelector(".font-select");
    if (select.value) {
      selectedFonts++;
      const sizeValue = row.querySelector(".size-value").value;
      const priceValue = row.querySelector(".price-value").value;
      fontData.push({ id: select.value, size: sizeValue, price: priceValue });
    }
  });
  if (selectedFonts < 2) {
    alert("You need to select at least 2 fonts in a group.");
    return;
  }
  const formData = new FormData();
  formData.append("id", groupId);
  formData.append("title", groupTitle);
  formData.append("font_data", JSON.stringify(fontData));
  fetch("api/update_font_group.php", { method: "POST", body: formData })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const modal = bootstrap.Modal.getInstance(
          document.getElementById("editGroupModal")
        );
        modal.hide();
        loadFontGroups();
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error updating font group:", error);
      alert("Failed to update font group. Please try again.");
    });
}
function deleteFontGroup(groupId) {
  if (!confirm("Are you sure you want to delete this font group?")) {
    return;
  }
  const formData = new FormData();
  formData.append("group_id", groupId);
  fetch("api/delete_font_group.php", { method: "POST", body: formData })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        loadFontGroups();
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error deleting font group:", error);
      alert("Failed to delete font group. Please try again.");
    });
}
