<?php
include('includes/checklogin.php');
check_login();

if (isset($_POST['save'])) {
    // Retrieve form data
    $category = $_POST['category'];
    $product = $_POST['product'];
    $price = $_POST['price'];
    $image = $_FILES["productimage"]["name"];
    $targetFile = "productimages/" . basename($image);

    // Initialize an array for error messages
    $errors = [];

    // Check if the image was uploaded successfully
    if (!move_uploaded_file($_FILES["productimage"]["tmp_name"], $targetFile)) {
        $errors[] = "Failed to upload image.";
    }

    // If there are no errors, proceed with database insertion
    if (empty($errors)) {
        try {
            // Prepare SQL query
            $sql = "INSERT INTO tblproducts (CategoryName, ProductName, ProductPrice, ProductImage) 
                    VALUES (:category, :product, :price, :image)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':category', $category, PDO::PARAM_STR);
            $query->bindParam(':product', $product, PDO::PARAM_STR);
            $query->bindParam(':price', $price, PDO::PARAM_STR);
            $query->bindParam(':image', $image, PDO::PARAM_STR);
            $query->execute();

            // Check if insertion was successful
            $lastInsertId = $dbh->lastInsertId();
            if ($lastInsertId > 0) {
                echo '<script>alert("Registered successfully")</script>';
                echo "<script>window.location.href ='product.php'</script>";
            } else {
                echo '<script>alert("No rows inserted. Please try again.")</script>';
            }
        } catch (PDOException $e) {
            // Output detailed error message
            echo '<script>alert("Database Error: ' . $e->getMessage() . '")</script>';
        }
    } else {
        // Display error messages
        foreach ($errors as $error) {
            echo '<script>alert("' . $error . '")</script>';
        }
    }
}

// Handle deletion of a product
if (isset($_GET['del'])) {
    $cmpid = $_GET['del'];
    try {
        $query = $dbh->prepare("DELETE FROM tblproducts WHERE id = :id");
        $query->bindParam(':id', $cmpid, PDO::PARAM_INT);
        $query->execute();
        echo "<script>alert('Product record deleted.');</script>";
        echo "<script>window.location.href='product.php'</script>";
    } catch (PDOException $e) {
        echo '<script>alert("Database Error: ' . $e->getMessage() . '")</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php @include("includes/head.php"); ?>
<body>
  <div class="container-scroller">
    <?php @include("includes/header.php"); ?>
    <div class="container-fluid page-body-wrapper">
      <?php @include("includes/sidebar.php"); ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="modal-header">
                  <h5 class="modal-title" style="float: left;">Register Product</h5>
                </div>
                <div class="col-md-12 mt-4">
                  <form class="forms-sample" method="post" enctype="multipart/form-data">
                    <div class="row">
                      <div class="form-group col-md-6">
                        <label for="category">Product Category</label>
                        <select name="category" class="form-control" required>
                          <option value="">Select Category</option>
                          <?php
                          $sql = "SELECT * FROM tblcategory";
                          $query = $dbh->prepare($sql);
                          $query->execute();
                          $results = $query->fetchAll(PDO::FETCH_OBJ);
                          if ($query->rowCount() > 0) {
                              foreach ($results as $row) {
                                  echo "<option value='{$row->CategoryName}'>{$row->CategoryName}</option>";
                              }
                          }
                          ?>
                        </select>
                      </div>
                      <div class="form-group col-md-6">
                        <label for="product">Product Name</label>
                        <input type="text" name="product" class="form-control" placeholder="Enter Product" required>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-md-6">
                        <label for="price">Product Price</label>
                        <input type="text" name="price" class="form-control" placeholder="Enter Price" required>
                      </div>
                      <div class="form-group col-md-6 pl-md-0">
                        <label class="col-sm-12 pl-0 pr-0">Attach Product Photo</label>
                        <div class="col-sm-12 pl-0 pr-0">
                          <input type="file" name="productimage" class="file-upload-default" required>
                          <div class="input-group">
                            <input type="text" class="form-control file-upload-info" disabled placeholder="Upload Image">
                            <span class="input-group-append">
                              <button class="file-upload-browse btn btn-gradient-primary" type="button">Upload</button>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <button type="submit" name="save" class="btn btn-primary mr-2 mb-4">Save</button>
                  </form>
                </div>
              </div>
            </div>
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush table-hover table-bordered" id="dataTableHover">
                    <thead>
                      <tr>
                        <th class="text-center">No</th>
                        <th>Product Name</th>
                        <th class="text-center">Product Category</th>
                        <th class="text-center">Product Price</th>
                        <th class="text-center">Posting Date</th>
                        <th class="text-center" style="width: 15%;">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT * FROM tblproducts ORDER BY id DESC";
                      $query = $dbh->prepare($sql);
                      $query->execute();
                      $results = $query->fetchAll(PDO::FETCH_OBJ);
                      $cnt = 1;
                      if ($query->rowCount() > 0) {
                          foreach ($results as $row) {
                              echo "<tr>
                                      <td class='text-center'>" . htmlentities($cnt) . "</td>
                                      <td>
                                          <img src='productimages/{$row->ProductImage}' class='mr-2' alt='image'>
                                          <a href='#' class='edit_data5' id='{$row->id}'>{$row->ProductName}</a>
                                      </td>
                                      <td class='text-center'>" . htmlentities($row->CategoryName) . "</td>
                                      <td class='text-center'>" . htmlentities($row->ProductPrice) . "</td>
                                      <td class='text-center'>" . htmlentities(date("d-m-Y", strtotime($row->PostingDate))) . "</td>
                                      <td class='text-center'>
                                          <a href='#' class='edit_data4' id='{$row->id}' title='click to edit'>
                                              <i class='mdi mdi-pencil-box-outline' aria-hidden='true'></i>
                                          </a>
                                          <a href='#' class='edit_data5' id='{$row->id}' title='click to view'>
                                              <i class='mdi mdi-eye' aria-hidden='true'></i>
                                          </a>
                                          <a href='product.php?del={$row->id}' data-toggle='tooltip' data-original-title='Delete' onclick=\"return confirm('Do you really want to delete?');\">
                                              <i class='mdi mdi-delete'></i>
                                          </a>
                                      </td>
                                  </tr>";
                              $cnt++;
                          }
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php @include("includes/footer.php"); ?>
      </div>
    </div>
  </div>
  <?php @include("includes/foot.php"); ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $(document).on('click', '.edit_data4', function() {
        var edit_id4 = $(this).attr('id');
        $.ajax({
          url: "edit_product.php",
          type: "post",
          data: { edit_id4: edit_id4 },
          success: function(data) {
            $("#info_update4").html(data);
            $("#editData4").modal('show');
          }
        });
      });
    });

    $(document).ready(function() {
      $(document).on('click', '.edit_data5', function() {
        var edit_id5 = $(this).attr('id');
        $.ajax({
          url: "view_product.php",
          type: "post",
          data: { edit_id5: edit_id5 },
          success: function(data) {
            $("#info_update5").html(data);
            $("#editData5").modal('show');
          }
        });
      });
    });
  </script>
</body>
</html>
