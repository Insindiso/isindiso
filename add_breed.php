<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (isset($_POST){

}
?>

<form class="forms-sample" method="post" enctype="multipart/form-data">
  <div class="row ">
    <div class="form-group col-md-12">
      <label for="breed">Breed</label>
      <input type="text" name="breed" class="form-control" id="breed" placeholder="Enter Broiler Breed" required>
    </div>
  </div>
  <button type="submit" style="float: left;" name="save" class="btn btn-primary mr-2 mb-4">Save</button>
  </form>