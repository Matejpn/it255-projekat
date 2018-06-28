import { Injectable } from '@angular/core';
import { apiUrl } from '../constants';
import "rxjs/add/operator/catch";
import "rxjs/add/operator/map";
import PostService from './post.service';

@Injectable()
export class RegisterService extends PostService {
  url = apiUrl + "registerservice.php";
}