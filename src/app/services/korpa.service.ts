import { Injectable } from '@angular/core';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import "rxjs/add/operator/do";
import { apiUrl, getAuthHeaders } from '../constants';
import { Http,Response } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import Product from '../models/product';

@Injectable()
export class KorpaService {

  protected urladd = apiUrl + 'korpaaddservice.php';
  protected urlremove = apiUrl + 'korparemoveservice.php';
  protected urlupdate = apiUrl + 'korpaupdateservice.php';
  protected urlcheckout = apiUrl + 'korpacheckoutservice.php';
  protected urlget = apiUrl + 'korpagetservice.php';

  constructor(protected http: Http) { }

  addToCart(product_id: number, quantity: number) {
    let data = "product_id=" + product_id + "&quantity=" + quantity;
    let headers = getAuthHeaders();
    this.http.post(this.urladd , data, {headers: headers})
        .map(res => res)
        .subscribe(data => {
            console.log(data)
        })
}

removeFromCart(product_id: number) {
  let data = "product_id=" + product_id;
  let headers = getAuthHeaders();
  this.http.post(this.urlremove, data, {headers: headers})
      .map(res => res)
      .subscribe(data => data)
}

updateInCart(product_id: number, quantity: number) {
  let data = "product_id=" + product_id + "&quantity=" + quantity;
  let headers = getAuthHeaders();
  this.http.post(this.urlupdate, data, {headers: headers})
      .map(res => res)
      .subscribe(data => data)
}

checkout() {
  let headers = getAuthHeaders();
  this.http.get(this.urlcheckout, {headers: headers})
      .map(res => res)
      .subscribe(data => data)
}

getCart(): Observable<Product[]> {
  let headers = getAuthHeaders();
  return this.http.get(this.urlget, {headers: headers})
      .map((response: Response) => <Product[]> response.json());
}
}
