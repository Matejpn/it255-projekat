import { Component, OnInit } from '@angular/core';
import Product from '../../models/product';
import User from '../../models/user';
import { FormGroup, FormControl } from '@angular/forms';
import { Http } from '@angular/http';
import { Router } from '@angular/router';
import { AdminService } from '../../services/admin.service';
import ProizvodiComponent from '../proizvodi/proizvodi.component';
import ProizvodiService from '../../services/proizvodi.service';
import { UserService } from '../../services/user.service';
import Narudzbina from '../../models/narudzbina';
import NarudzbineService from '../../services/narudzbine.service';

@Component({
  selector: 'app-admin',
  templateUrl: './admin.component.html',
  styleUrls: ['./admin.component.css']
})
export default class AdminComponent implements OnInit {
  
  proizvodi : Product[];
  narudzbine : Narudzbina[];
  user : User;
  addProductForm = new FormGroup({

      ime: new FormControl(),
      katID: new FormControl(),
      opis: new FormControl(),
      cena: new FormControl(),
      url: new FormControl(),
      brand_id: new FormControl(),
      // idProizvoda: new FormControl(),
      // idNarudzbine: new FormControl(),
      // kolicina:new FormControl(),
      // idKorpe:new FormControl(),
     


  });

   constructor( private http: Http,private router: Router, private adminService: AdminService, private proizvodiService: ProizvodiService, private narudzbineService:NarudzbineService, private _userService : UserService) {
        if (!localStorage.getItem('token')) {
            this.router.navigate(['/']);

        }

    }

    addProduct(model: Product) {
      this.adminService.callService(model).subscribe(data => {
          this.router.navigate(['/']);
      });

  }

  ngOnInit() {
    this.loadProizvodi();
    this.loadNarudzbine();
  }

  loadProizvodi() {
    // var $:any;
    // this.proizvodiService.getProizvodi().subscribe(data => {
    //     this.proizvodi = data;
    //     setInterval(function(){
    //         $ = window['jQuery'];
    //         $('table').DataTable();
    //     }.bind(this),400);
    // });
    this.proizvodiService.getProizvodi().subscribe(data => {
      this.proizvodi = data;
    });

}

loadNarudzbine() {
  // var $:any;
  // this.proizvodiService.getProizvodi().subscribe(data => {
  //     this.proizvodi = data;
  //     setInterval(function(){
  //         $ = window['jQuery'];
  //         $('table').DataTable();
  //     }.bind(this),400);
  // });
  this.narudzbineService.getNarudzbine().subscribe(data => {
    this.narudzbine = data;
  });

}

remove(id:number){
  this.adminService.removeProduct(id);



  }

}
