//
//  SubCateViewController.h
//  ipapp
//
//  Created by kelaocai on 13-9-18.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "CateViewController.h"

@interface SubCateViewController : UIViewController
@property(strong,nonatomic) NSArray *subCates;
@property(strong,nonatomic)  CateViewController *cateVc;
@end
