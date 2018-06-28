import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import { Injectable } from '@angular/core';
import { apiUrl, getAuthHeaders } from '../constants';
import PostService from './post.service';
import { Observable } from 'rxjs/Observable';

@Injectable()
export class AdminService extends PostService {
  url = apiUrl + 'adminaddservice.php';
  urlremove = apiUrl + 'adminremoveservice.php';
  

  callService(item: Object) : Observable<void> {
    this.headers = getAuthHeaders();
    return super.callService(item);
}

removeProduct(id: number) {
  let data = "id=" + id;
  let headers = getAuthHeaders();
  this.http.post(this.urlremove, data, {headers: headers})
      .map(res => res)
      .subscribe(data => data)
}
}
