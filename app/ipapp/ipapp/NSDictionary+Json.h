//
//  NSDictionary+Json.h
//  ipapp
//
//  Created by kelaocai on 13-10-23.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface NSDictionary (Json)
// 直把远程的地址上Json数据转,换成Dictionary对象
+(NSDictionary*)dictionaryWithContentsOfURLString:(NSString*)urlAddress;

// 把当前的Dictionary对象,转成Json对象
-(NSData*)toJSON;

@end
