import {Pipe} from '@angular/core';

@Pipe({
  name: 'SearchPipeName'
})

export class SearchPipeName {
  transform(value: Object[], anotherValue: string): Object[] {
    if(value == null){
      return null;
    }
    if(anotherValue !== undefined){
      return value.filter((item: Object) => item["ime"].indexOf(anotherValue) !== -1);
    }else{
      return value;
    }
  }
}
