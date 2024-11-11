<?php
include('includes/checklogin.php');
check_login();

if (isset($_POST['save'])) {
    // Retrieve the posted data
    $category = $_POST['category'];
    $code = $_POST['code'];

    // Prepare the SQL statement
    $sql = "INSERT INTO tblcategory (CategoryName, CategoryCode) VALUES (:category, :code)";
    $query = $dbh->prepare($sql);

    // Bind parameters
    $query->bindParam(':category', $category, PDO::PARAM_STR);
    $query->bindParam(':code', $code, PDO::PARAM_STR);

    // Execute the query
    try {
        $query->execute();
        $LastInsertId = $dbh->lastInsertId();

        // Check if the record was inserted
        if ($LastInsertId > 0) {
            echo '<script>alert("Registered successfully")</script>';
            echo "<script>window.location.href ='category.php'</script>";
        } else {
            echo '<script>alert("Something went wrong. Please try again.")</script>';
        }
    } catch (PDOException $e) {
        // Catch any database errors and display them
        echo '<script>alert("Error: ' . $e->getMessage() . '")</script>';
    }
}

// Handle deletion of a category
if (isset($_GET['del'])) {    
    $cmpid = $_GET['del'];
    $query = $dbh->prepare("DELETE FROM tblcategory WHERE id = :id");
    $query->bindParam(':id', $cmpid, PDO::PARAM_INT);
    $query->execute();
    echo "<script>alert('Category record deleted.');</script>";   
    echo "<script>window.location.href='category.php'</script>";
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
                <h5 class="modal-title" style="float: left;">Category Register</h5>
              </div>
              <div class="col-md-12 mt-4">
                <form class="forms-sample" method="post" enctype="multipart/form-data">
                  <div class="row ">
                    <div class="form-group col-md-6">
                      <label for="category">Category</label>
                      <input type="text" name="category" class="form-control" id="category" placeholder="Enter Category" required>
                    </div>
                  </div>
                  <div class="row ">
                    <div class="form-group col-md-6">
                      <label for="code">Code</label>
                      <input type="text" name="code" class="form-control" id="code" placeholder="Enter Code" required>
                    </div>
                  </div>
                  <button type="submit" style="float: left;" name="save" class="btn btn-primary mr-2 mb-4">Save</button>
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
                      <th>Category</th>
                      <th class="text-center">Category Code</th>
                      <th class="text-center">Posting Date</th>
                      <th class="text-center" style="width: 15%;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql = "SELECT id, CategoryName, CategoryCode, PostingDate FROM tblcategory ORDER BY id DESC";
                    $query = $dbh->prepare($sql);
                    $query->execute();
                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                    $cnt = 1;
                    if ($query->rowCount() > 0) {
                      foreach ($results as $row) { 
                        ?>
                        <tr>
                          <td class="text-center"><?php echo htmlentities($cnt); ?></td>
                          <td><a href="#" class="edit_data5" id="<?php echo ($row->id); ?>"><?php echo htmlentities($row->CategoryName); ?></a></td>
                          <td class="text-center"><?php echo htmlentities($row->CategoryCode); ?></td>
                          <td class="text-center"><?php echo htmlentities(date("d-m-Y", strtotime($row->PostingDate))); ?></td>
                          <td class="text-center">
                            <a href="#" class="edit_data4" id="<?php echo ($row->id); ?>" title="click to edit"><i class="mdi mdi-pencil-box-outline" aria-hidden="true"></i></a>
                            <a href="#" class="edit_data5" id="<?php echo ($row->id); ?>" title="click to view">&nbsp;<i class="mdi mdi-eye" aria-hidden="true"></i></a>
                            <a href="category.php?del=<?php echo $row->id; ?>" data-toggle="tooltip" data-original-title="Delete" onclick="return confirm('Do you really want to delete?');"><i class="mdi mdi-delete"></i></a>
                          </td>
                        </tr>
                        <?php 
                        $cnt++;
                      }
                    } ?>
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
  <?php @include("includes/foot.php"); ?>
  <script type="text/javascript">
    $(document).ready(function(){
      $(document).on('click','.edit_data4',function(){
        var edit_id4 = $(this).attr('id');
        $.ajax({
          url: "edit_category.php",
          type: "post",
          data: {edit_id4: edit_id4},
          success: function(data){
            $("#info_update4").html(data);
            $("#editData4").modal('show');
          }
        });
      });
    });

    $(document).ready(function(){
      $(document).on('click','.edit_data5',function(){
        var edit_id5 = $(this).attr('id');
        $.ajax({
          url: "view_category.php",
          type: "post",
          data: {edit_id5: edit_id5},
          success: function(data){
            $("#info_update5").html(data);
            $("#editData5").modal('show');
          }
        });
      });
    });
  </script>
</body>
</html>
