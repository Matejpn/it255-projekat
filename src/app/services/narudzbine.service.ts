import { Injectable } from '@angular/core';
import { apiUrl, getAuthHeaders } from '../constants';
import { Http,Response } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import Narudzbina from '../models/narudzbina';

@Injectable()
export default class NarudzbineService {

  protected url = apiUrl + "getNarudzbine.php";

  constructor(protected http: Http) { }

  getNarudzbine(): Observable<Narudzbina[]> {
    return this.http.get(this.url, {headers: getAuthHeaders() })
        .map(this.extractData)
}

protected extractData(res: Response) {
  let obj = JSON.parse(res['_body']);
  return obj.narudzbine;
}
}
