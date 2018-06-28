import { Observable } from 'rxjs/Observable';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';

import {Injectable} from "@angular/core";
import {Http, Response} from "@angular/http";
import {apiUrl, getAuthHeaders} from "../constants";
import Product from '../models/product';


@Injectable()
export default class ProizvodService {
  protected url = apiUrl + "getproizvod.php?id=";

  constructor (protected http: Http) {}

  getProizvod(id: Number): Observable<Product[]> {
    return this.http.get(this.url+id, {headers: getAuthHeaders() })
      .map(this.extractData)
  }
  protected extractData(res: Response) {
    let obj = JSON.parse(res['_body']);
    return obj.proizvodi;
  }


}
