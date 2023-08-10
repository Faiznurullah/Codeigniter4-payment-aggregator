<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Bootstrap demo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>
<body>

<div class="row justify-content-center mt-5">
<div class="col-xl-11">
<form action="/coba" method="POST">
<?= csrf_field() ?>
<div class="row">
<div class="col-xl-6">
<label for="exampleInputEmail1" class="form-label">Email address</label>
<input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
</div>
<div class="col-xl-6">
<label for="amount" class="form-label">Amount</label>
<input type="number" class="form-control" id="amount" name="amount" min="1">
</div>
</div>
<div class="row">
<div class="col-xl-6">
<label for="phone" class="form-label">Phone</label>
<input type="number" class="form-control" id="phone" name="phone" min="1">
</div>
<div class="col-xl-6 mt-4">
<button class="btn btn-primary btn-sm" type="submit">Submit</button>
</div>
</div>
</form>
</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>
</html>