<table id="example" class="display dataTable" style="width: 100%;" role="grid" aria-describedby="example_info">
   <thead>
      <tr role="row">
         <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Name: activate to sort column ascending" style="width: 68px;">Name</th>
         <th class="sorting_desc" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" aria-sort="descending" style="width: 105px;">Position</th>
         <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 53px;">Office</th>
         <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 28px;">Age</th>
         <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Start date: activate to sort column ascending" style="width: 56px;">Start date</th>
         <th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Salary: activate to sort column ascending" style="width: 57px;">Salary</th>
      </tr>
   </thead>
   <tbody>
      <tr role="row" class="odd">
         <td class="">Prescott Bartlett</td>
         <td class="sorting_1">Technical Author</td>
         <td>London</td>
         <td>27</td>
         <td>2011/05/07</td>
         <td>$145,000</td>
      </tr>
      <tr role="row" class="even">
         <td class="">Gavin Cortez</td>
         <td class="sorting_1">Team Leader</td>
         <td>San Francisco</td>
         <td>22</td>
         <td>2008/10/26</td>
         <td>$235,500</td>
      </tr>
      <tr role="row" class="odd">
         <td class="">Gloria Little</td>
         <td class="sorting_1">Systems Administrator</td>
         <td>New York</td>
         <td>59</td>
         <td>2009/04/10</td>
         <td>$237,500</td>
      </tr>
      <tr role="row" class="even">
         <td class="">Lael Greer</td>
         <td class="sorting_1">Systems Administrator</td>
         <td>London</td>
         <td>21</td>
         <td>2009/02/27</td>
         <td>$103,500</td>
      </tr>
      <tr role="row" class="odd">
         <td class="">Tiger Nixon</td>
         <td class="sorting_1">System Architect</td>
         <td>Edinburgh</td>
         <td>61</td>
         <td>2011/04/25</td>
         <td>$320,800</td>
      </tr>
      <tr role="row" class="even">
         <td class="">Quinn Flynn</td>
         <td class="sorting_1">Support Lead</td>
         <td>Edinburgh</td>
         <td>22</td>
         <td>2013/03/03</td>
         <td>$342,000</td>
      </tr>
      <tr role="row" class="odd">
         <td class="">Finn Camacho</td>
         <td class="sorting_1">Support Engineer</td>
         <td>San Francisco</td>
         <td>47</td>
         <td>2009/07/07</td>
         <td>$87,500</td>
      </tr>
      <tr role="row" class="even">
         <td class="">Olivia Liang</td>
         <td class="sorting_1">Support Engineer</td>
         <td>Singapore</td>
         <td>64</td>
         <td>2011/02/03</td>
         <td>$234,500</td>
      </tr>
      <tr role="row" class="odd">
         <td class="">Sakura Yamamoto</td>
         <td class="sorting_1">Support Engineer</td>
         <td>Tokyo</td>
         <td>37</td>
         <td>2009/08/19</td>
         <td>$139,575</td>
      </tr>
      <tr role="row" class="even">
         <td class="">Bradley Greer</td>
         <td class="sorting_1">Software Engineer</td>
         <td>London</td>
         <td>41</td>
         <td>2012/10/13</td>
         <td>$132,000</td>
      </tr>
   </tbody>
   <tfoot>
      <tr>
         <th rowspan="1" colspan="1">Name</th>
         <th rowspan="1" colspan="1">Position</th>
         <th rowspan="1" colspan="1">Office</th>
         <th rowspan="1" colspan="1">Age</th>
         <th rowspan="1" colspan="1">Start date</th>
         <th rowspan="1" colspan="1">Salary</th>
      </tr>
   </tfoot>
</table>

@section('stylesheet')
    @parent

    <link rel="stylesheet" href="{{ asset('vendor/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.css') }}">

@endsection

@section('scripts')
   @parent

   <script src="{{ asset("vendor/datatables/DataTables-1.10.16/js/jquery.dataTables.js") }}"></script>
   <script src="{{ asset("vendor/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.js") }}"></script>

   <script type="text/javascript" class="init">
   

      $(document).ready(function() {
         $('#example').DataTable();
      } );


   </script>

@endsection